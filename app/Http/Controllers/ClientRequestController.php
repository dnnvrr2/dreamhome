<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_no' => 'required|exists:properties,property_no',
            'f_name' => 'required|max:30',
            'l_name' => 'required|max:40',
            'email' => 'nullable|email|max:120',
            'tel_no' => 'required|max:20',
            'street' => 'nullable|max:60',
            'area' => 'nullable|max:30',
            'city' => 'nullable|max:30',
            'postcode' => 'nullable|max:10',
            'pref_type' => 'nullable|max:20',
            'max_rent' => 'nullable|numeric|min:0',
            'preferred_view_date' => 'nullable|date|after_or_equal:today',
            'comments' => 'nullable',
        ]);

        $isAvailable = DB::selectOne(
            "SELECT fn_is_property_available(?::varchar) AS is_available",
            [$validated['property_no']]
        )->is_available;

        if (!$isAvailable) {
            return back()->withInput()
                ->with('error', 'This property is no longer available for public requests.');
        }

        DB::insert("
            INSERT INTO client_requests
                (property_no, f_name, l_name, email, tel_no, street, area, city, postcode,
                 pref_type, max_rent, preferred_view_date, comments, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())
        ", [
            $validated['property_no'],
            $validated['f_name'],
            $validated['l_name'],
            $validated['email'] ?? null,
            $validated['tel_no'],
            $validated['street'] ?? null,
            $validated['area'] ?? null,
            $validated['city'] ?? null,
            $validated['postcode'] ?? null,
            $validated['pref_type'] ?? null,
            $validated['max_rent'] ?? null,
            $validated['preferred_view_date'] ?? null,
            $validated['comments'] ?? null,
        ]);

        return redirect()->route('public.properties')
            ->with('success', 'Your request has been sent. Our team will review it and contact you after approval.');
    }

    public function index(Request $request)
    {
        $status = $request->status ?? 'pending';

        $sql = "
            SELECT cr.*, p.street AS property_street, p.city AS property_city, p.type AS property_type,
                   p.rent AS property_rent
            FROM client_requests cr
            LEFT JOIN properties p ON cr.property_no = p.property_no
            WHERE 1=1
        ";

        $params = [];

        if ($status !== 'all') {
            $sql .= " AND cr.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY cr.created_at DESC";

        $requests = DB::select($sql, $params);

        return view('client_requests.index', compact('requests', 'status'));
    }

    public function approve($id)
    {
        $clientRequest = DB::select("
            SELECT cr.*, p.branch_no, p.type AS property_type, p.rent AS property_rent
            FROM client_requests cr
            LEFT JOIN properties p ON cr.property_no = p.property_no
            WHERE cr.id = ?
        ", [$id]);

        $clientRequest = $clientRequest[0] ?? abort(404);

        if ($clientRequest->status !== 'pending') {
            return redirect()->route('client-requests.index')
                ->with('success', 'This request has already been processed.');
        }

        $clientNo = $this->nextClientNo();
        $prefType = $clientRequest->pref_type ?: $clientRequest->property_type;
        $maxRent = $clientRequest->max_rent ?: $clientRequest->property_rent;

        try {
            DB::transaction(function () use ($clientRequest, $clientNo, $prefType, $maxRent) {
                DB::insert("
                    INSERT INTO clients
                        (client_no, f_name, l_name, street, area, city, postcode, tel_no, pref_type,
                         max_rent, comments, registered_by, branch_no, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, NOW(), NOW())
                ", [
                    $clientNo,
                    $clientRequest->f_name,
                    $clientRequest->l_name,
                    $clientRequest->street,
                    $clientRequest->area,
                    $clientRequest->city,
                    $clientRequest->postcode,
                    $clientRequest->tel_no,
                    $prefType,
                    $maxRent,
                    trim(($clientRequest->comments ?? '') . "\nEmail: " . ($clientRequest->email ?? 'N/A')),
                    $clientRequest->branch_no,
                ]);

                if ($clientRequest->preferred_view_date && $clientRequest->property_no) {
                    DB::insert("
                        INSERT INTO viewings
                            (client_no, property_no, view_date, staff_no, comments, created_at, updated_at)
                        VALUES (?, ?, ?, NULL, ?, NOW(), NOW())
                    ", [
                        $clientNo,
                        $clientRequest->property_no,
                        $clientRequest->preferred_view_date,
                        'Requested from public property page.',
                    ]);
                }

                DB::update("
                    UPDATE client_requests
                    SET status = 'approved',
                        approved_client_no = ?,
                        processed_by = ?,
                        processed_at = NOW(),
                        updated_at = NOW()
                    WHERE id = ?
                ", [
                    $clientNo,
                    Auth::id(),
                    $clientRequest->id,
                ]);
            });
        } catch (QueryException $exception) {
            return back()->with('error', $this->databaseError($exception));
        }

        return redirect()->route('client-requests.index')
            ->with('success', 'Request approved and client record created.');
    }

    public function reject($id)
    {
        try {
            DB::update("
                UPDATE client_requests
                SET status = 'rejected',
                    processed_by = ?,
                    processed_at = NOW(),
                    updated_at = NOW()
                WHERE id = ? AND status = 'pending'
            ", [Auth::id(), $id]);
        } catch (QueryException $exception) {
            return back()->with('error', $this->databaseError($exception));
        }

        return redirect()->route('client-requests.index')
            ->with('success', 'Request rejected.');
    }

    private function nextClientNo(): string
    {
        $lastClient = DB::selectOne("
            SELECT client_no
            FROM clients
            WHERE client_no LIKE 'C%'
            ORDER BY client_no DESC
        ");

        $nextNumber = $lastClient ? ((int) substr($lastClient->client_no, 1)) + 1 : 1;

        do {
            $clientNo = 'C' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = DB::selectOne("SELECT client_no FROM clients WHERE client_no = ?", [$clientNo]);
            $nextNumber++;
        } while ($exists);

        return $clientNo;
    }
}
