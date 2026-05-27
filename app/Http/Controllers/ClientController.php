<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $branch_no    = $request->branch_no;
        $pref_type    = $request->pref_type;
        $max_rent     = $request->max_rent;

        $sql = "
            SELECT c.*,
                b.city AS branch_city,
                s.f_name AS staff_fname,
                s.l_name AS staff_lname
            FROM clients c
            LEFT JOIN branches b ON c.branch_no = b.branch_no
            LEFT JOIN staff s ON c.registered_by = s.staff_no
            WHERE 1=1
        ";

        $params = [];

        if ($branch_no) {
            $sql .= " AND c.branch_no = ?";
            $params[] = $branch_no;
        }

        if ($pref_type) {
            $sql .= " AND c.pref_type = ?";
            $params[] = $pref_type;
        }

        if ($max_rent) {
            $sql .= " AND c.max_rent <= ?";
            $params[] = $max_rent;
        }

        $sql .= " ORDER BY c.client_no ASC";

        $clients  = DB::select($sql, $params);
        $branches = DB::select("SELECT * FROM branches ORDER BY branch_no");
        $staff    = DB::select("SELECT * FROM staff ORDER BY staff_no");

        return view('clients.index', compact('clients', 'branches', 'staff'));
    }

    public function create()
    {
        $branches = DB::select("SELECT * FROM branches ORDER BY branch_no");
        $staff    = DB::select("SELECT * FROM staff ORDER BY staff_no");
        $clientNo = $this->nextPrefixedId('clients', 'client_no', 'CR', 3);
        return view('clients.create', compact('branches', 'staff', 'clientNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'f_name'    => 'required|max:30',
            'l_name'    => 'required|max:40',
        ]);

        $clientNo = $this->nextPrefixedId('clients', 'client_no', 'CR', 3);

        DB::insert("
            INSERT INTO clients
                (client_no, f_name, l_name, street, area, city, postcode,
                 tel_no, pref_type, max_rent, comments, registered_by, branch_no,
                 created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $clientNo,
            $request->f_name,
            $request->l_name,
            $request->street,
            $request->area,
            $request->city,
            $request->postcode,
            $request->tel_no,
            $request->pref_type,
            $request->max_rent,
            $request->comments,
            $request->registered_by,
            $request->branch_no,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client registered successfully.');
    }

    public function edit($client_no)
    {
        $client = DB::select("SELECT * FROM clients WHERE client_no = ?", [$client_no]);
        $client = $client[0] ?? abort(404);

        $branches = DB::select("SELECT * FROM branches ORDER BY branch_no");
        $staff    = DB::select("SELECT * FROM staff ORDER BY staff_no");

        return view('clients.edit', compact('client', 'branches', 'staff'));
    }

    public function update(Request $request, $client_no)
    {
        $request->validate([
            'f_name' => 'required|max:30',
            'l_name' => 'required|max:40',
        ]);

        DB::update("
            UPDATE clients
            SET f_name        = ?,
                l_name        = ?,
                street        = ?,
                area          = ?,
                city          = ?,
                postcode      = ?,
                tel_no        = ?,
                pref_type     = ?,
                max_rent      = ?,
                comments      = ?,
                registered_by = ?,
                branch_no     = ?,
                updated_at    = NOW()
            WHERE client_no = ?
        ", [
            $request->f_name,
            $request->l_name,
            $request->street,
            $request->area,
            $request->city,
            $request->postcode,
            $request->tel_no,
            $request->pref_type,
            $request->max_rent,
            $request->comments,
            $request->registered_by,
            $request->branch_no,
            $client_no,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy($client_no)
    {
        DB::delete("DELETE FROM clients WHERE client_no = ?", [$client_no]);

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
