<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ViewingController extends Controller
{
    public function index(Request $request)
    {
        $property_no = $request->property_no;
        $client_no   = $request->client_no;

        $sql = "
            SELECT v.*,
                   c.f_name AS client_fname, c.l_name AS client_lname,
                   p.street AS property_street, p.city AS property_city,
                   s.f_name AS staff_fname, s.l_name AS staff_lname
            FROM viewings v
            LEFT JOIN clients c ON v.client_no = c.client_no
            LEFT JOIN properties p ON v.property_no = p.property_no
            LEFT JOIN staff s ON v.staff_no = s.staff_no
            WHERE 1=1
        ";

        $params = [];

        if ($property_no) {
            $sql .= " AND v.property_no = ?";
            $params[] = $property_no;
        }

        if ($client_no) {
            $sql .= " AND v.client_no = ?";
            $params[] = $client_no;
        }

        $sql .= " ORDER BY v.view_date DESC";

        $viewings    = DB::select($sql, $params);
        $properties  = DB::select("SELECT * FROM properties ORDER BY property_no");
        $clients     = DB::select("SELECT * FROM clients ORDER BY client_no");

        return view('viewings.index', compact('viewings', 'properties', 'clients'));
    }

    public function create()
    {
        $properties = DB::select("SELECT * FROM properties WHERE is_available = true ORDER BY property_no");
        $clients    = DB::select("SELECT * FROM clients ORDER BY client_no");
        $staff      = DB::select("SELECT * FROM staff ORDER BY staff_no");
        return view('viewings.create', compact('properties', 'clients', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_no'   => 'required',
            'property_no' => 'required',
            'view_date'   => 'required|date',
        ]);

        DB::insert("
            INSERT INTO viewings (client_no, property_no, view_date, staff_no, comments, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $request->client_no,
            $request->property_no,
            $request->view_date,
            $request->staff_no ?: null,
            $request->comments,
        ]);

        return redirect()->route('viewings.index')
            ->with('success', 'Viewing scheduled successfully.');
    }

    public function edit(Request $request, $client_no)
    {
        $property_no = $request->property_no;
        $view_date   = $request->view_date;

        $viewing = DB::select("
            SELECT * FROM viewings
            WHERE client_no = ? AND property_no = ? AND view_date = ?
        ", [$client_no, $property_no, $view_date]);

        $viewing    = $viewing[0] ?? abort(404);
        $properties = DB::select("SELECT * FROM properties ORDER BY property_no");
        $clients    = DB::select("SELECT * FROM clients ORDER BY client_no");
        $staff      = DB::select("SELECT * FROM staff ORDER BY staff_no");

        return view('viewings.edit', compact('viewing', 'properties', 'clients', 'staff'));
    }

    public function update(Request $request, $client_no)
    {
        $request->validate([
            'view_date' => 'required|date',
        ]);

        DB::update("
            UPDATE viewings
            SET staff_no   = ?,
                comments   = ?,
                updated_at = NOW()
            WHERE client_no = ? AND property_no = ? AND view_date = ?
        ", [
            $request->staff_no ?: null,
            $request->comments,
            $client_no,
            $request->property_no,
            $request->view_date,
        ]);

        return redirect()->route('viewings.index')
            ->with('success', 'Viewing updated successfully.');
    }

    public function destroy(Request $request, $client_no)
    {
        DB::delete("
            DELETE FROM viewings
            WHERE client_no = ? AND property_no = ? AND view_date = ?
        ", [$client_no, $request->property_no, $request->view_date]);

        return redirect()->route('viewings.index')
            ->with('success', 'Viewing deleted successfully.');
    }
}