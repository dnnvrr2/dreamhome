<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaseController extends Controller
{
    public function index(Request $request)
    {
        $client_no   = $request->client_no;
        $property_no = $request->property_no;

        $sql = "
            SELECT l.*,
                   c.f_name AS client_fname, c.l_name AS client_lname,
                   p.street AS property_street, p.city AS property_city,
                   s.f_name AS staff_fname, s.l_name AS staff_lname
            FROM leases l
            LEFT JOIN clients c ON l.client_no = c.client_no
            LEFT JOIN properties p ON l.property_no = p.property_no
            LEFT JOIN staff s ON l.staff_no = s.staff_no
            WHERE 1=1
        ";

        $params = [];

        if ($client_no) {
            $sql .= " AND l.client_no = ?";
            $params[] = $client_no;
        }

        if ($property_no) {
            $sql .= " AND l.property_no = ?";
            $params[] = $property_no;
        }

        $sql .= " ORDER BY l.date_start DESC";

        $leases     = DB::select($sql, $params);
        $clients    = DB::select("SELECT * FROM clients ORDER BY client_no");
        $properties = DB::select("SELECT * FROM properties ORDER BY property_no");

        return view('leases.index', compact('leases', 'clients', 'properties'));
    }

    public function create()
    {
        $clients    = DB::select("SELECT * FROM clients ORDER BY client_no");
        $properties = DB::select("SELECT * FROM properties WHERE is_available = true ORDER BY property_no");
        $staff      = DB::select("SELECT * FROM staff ORDER BY staff_no");
        return view('leases.create', compact('clients', 'properties', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lease_no'      => 'required|max:5|unique:leases',
            'client_no'     => 'required',
            'property_no'   => 'required',
            'date_start'    => 'required|date',
            'date_end'      => 'required|date|after:date_start',
            'monthly_rent'  => 'required|numeric',
            'duration_month'=> 'required|integer|min:3|max:12',
        ]);

        DB::insert("
            INSERT INTO leases
                (lease_no, monthly_rent, payment_method, deposit, deposit_paid,
                 date_start, date_end, duration_month, client_no, property_no, staff_no,
                 created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $request->lease_no,
            $request->monthly_rent,
            $request->payment_method,
            $request->deposit,
            $request->deposit_paid ? true : false,
            $request->date_start,
            $request->date_end,
            $request->duration_month,
            $request->client_no,
            $request->property_no,
            $request->staff_no ?: null,
        ]);

        return redirect()->route('leases.index')
            ->with('success', 'Lease created successfully.');
    }

    public function show($lease_no)
    {
        $lease = DB::select("
            SELECT l.*,
                   c.f_name AS client_fname, c.l_name AS client_lname,
                   p.street AS property_street, p.city AS property_city,
                   s.f_name AS staff_fname, s.l_name AS staff_lname
            FROM leases l
            LEFT JOIN clients c ON l.client_no = c.client_no
            LEFT JOIN properties p ON l.property_no = p.property_no
            LEFT JOIN staff s ON l.staff_no = s.staff_no
            WHERE l.lease_no = ?
        ", [$lease_no]);

        $lease = $lease[0] ?? abort(404);
        return view('leases.show', compact('lease'));
    }

    public function edit($lease_no)
    {
        $lease      = DB::select("SELECT * FROM leases WHERE lease_no = ?", [$lease_no]);
        $lease      = $lease[0] ?? abort(404);
        $clients    = DB::select("SELECT * FROM clients ORDER BY client_no");
        $properties = DB::select("SELECT * FROM properties ORDER BY property_no");
        $staff      = DB::select("SELECT * FROM staff ORDER BY staff_no");
        return view('leases.edit', compact('lease', 'clients', 'properties', 'staff'));
    }

    public function update(Request $request, $lease_no)
    {
        $request->validate([
            'date_start'    => 'required|date',
            'date_end'      => 'required|date|after:date_start',
            'monthly_rent'  => 'required|numeric',
            'duration_month'=> 'required|integer|min:3|max:12',
        ]);

        DB::update("
            UPDATE leases
            SET monthly_rent   = ?,
                payment_method = ?,
                deposit        = ?,
                deposit_paid   = ?,
                date_start     = ?,
                date_end       = ?,
                duration_month = ?,
                client_no      = ?,
                property_no    = ?,
                staff_no       = ?,
                updated_at     = NOW()
            WHERE lease_no = ?
        ", [
            $request->monthly_rent,
            $request->payment_method,
            $request->deposit,
            $request->deposit_paid ? true : false,
            $request->date_start,
            $request->date_end,
            $request->duration_month,
            $request->client_no,
            $request->property_no,
            $request->staff_no ?: null,
            $lease_no,
        ]);

        return redirect()->route('leases.index')
            ->with('success', 'Lease updated successfully.');
    }

    public function destroy($lease_no)
    {
        DB::delete("DELETE FROM leases WHERE lease_no = ?", [$lease_no]);
        return redirect()->route('leases.index')
            ->with('success', 'Lease deleted successfully.');
    }
}