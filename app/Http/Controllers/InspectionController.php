<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        $property_no = $request->property_no;
        $staff_no    = $request->staff_no;

        $sql = "
            SELECT i.*,
                   p.street AS property_street, p.city AS property_city,
                   s.f_name AS staff_fname, s.l_name AS staff_lname
            FROM inspections i
            LEFT JOIN properties p ON i.property_no = p.property_no
            LEFT JOIN staff s ON i.staff_no = s.staff_no
            WHERE 1=1
        ";

        $params = [];

        if ($property_no) {
            $sql .= " AND i.property_no = ?";
            $params[] = $property_no;
        }

        if ($staff_no) {
            $sql .= " AND i.staff_no = ?";
            $params[] = $staff_no;
        }

        $sql .= " ORDER BY i.inspection_date DESC";

        $inspections = DB::select($sql, $params);
        $properties  = DB::select("SELECT * FROM properties ORDER BY property_no");
        $staff       = DB::select("SELECT * FROM staff ORDER BY staff_no");

        return view('inspections.index', compact('inspections', 'properties', 'staff'));
    }

    public function create()
    {
        $properties = DB::select("SELECT * FROM properties WHERE is_available = true ORDER BY property_no");
        $staff      = DB::select("SELECT * FROM staff ORDER BY staff_no");
        return view('inspections.create', compact('properties', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_no'     => 'required',
            'staff_no'        => 'required',
            'inspection_date' => 'required|date',
        ]);

        DB::insert("
            INSERT INTO inspections (property_no, staff_no, inspection_date, comments, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ", [
            $request->property_no,
            $request->staff_no,
            $request->inspection_date,
            $request->comments,
        ]);

        return redirect()->route('inspections.index')
            ->with('success', 'Inspection recorded successfully.');
    }

    public function edit($inspection_id)
    {
        $inspection = DB::select("SELECT * FROM inspections WHERE inspection_id = ?", [$inspection_id]);
        $inspection = $inspection[0] ?? abort(404);
        $properties = DB::select("SELECT * FROM properties ORDER BY property_no");
        $staff      = DB::select("SELECT * FROM staff ORDER BY staff_no");
        return view('inspections.edit', compact('inspection', 'properties', 'staff'));
    }

    public function update(Request $request, $inspection_id)
    {
        $request->validate([
            'property_no'     => 'required',
            'staff_no'        => 'required',
            'inspection_date' => 'required|date',
        ]);

        DB::update("
            UPDATE inspections
            SET property_no     = ?,
                staff_no        = ?,
                inspection_date = ?,
                comments        = ?,
                updated_at      = NOW()
            WHERE inspection_id = ?
        ", [
            $request->property_no,
            $request->staff_no,
            $request->inspection_date,
            $request->comments,
            $inspection_id,
        ]);

        return redirect()->route('inspections.index')
            ->with('success', 'Inspection updated successfully.');
    }

    public function destroy($inspection_id)
    {
        DB::delete("DELETE FROM inspections WHERE inspection_id = ?", [$inspection_id]);
        return redirect()->route('inspections.index')
            ->with('success', 'Inspection deleted successfully.');
    }
}