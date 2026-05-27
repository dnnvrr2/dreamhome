<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        $property_no = $request->property_no;
        $staff_no    = $request->staff_no;

        $sql = "
            SELECT i.inspection_id, i.property_no, i.staff_no,
                   v.inspection_date, v.comments,
                   v.street AS property_street, v.city AS property_city,
                   v.inspector_fname AS staff_fname, v.inspector_lname AS staff_lname
            FROM inspections i
            LEFT JOIN vw_inspection_report v ON i.inspection_id = v.inspection_id
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

        try {
            DB::statement("CALL sp_record_inspection(?::varchar, ?::varchar, ?::date, ?::text)", [
                $request->property_no,
                $request->staff_no,
                $request->inspection_date,
                $request->comments,
            ]);
        } catch (QueryException $exception) {
            return back()->withInput()->with('error', $this->databaseError($exception));
        }

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

        try {
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
        } catch (QueryException $exception) {
            return back()->withInput()->with('error', $this->databaseError($exception));
        }

        return redirect()->route('inspections.index')
            ->with('success', 'Inspection updated successfully.');
    }

    public function destroy($inspection_id)
    {
        try {
            DB::delete("DELETE FROM inspections WHERE inspection_id = ?", [$inspection_id]);
        } catch (QueryException $exception) {
            return back()->with('error', $this->databaseError($exception));
        }

        return redirect()->route('inspections.index')
            ->with('success', 'Inspection deleted successfully.');
    }
}
