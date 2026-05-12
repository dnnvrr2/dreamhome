<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionApiController extends Controller
{
    public function index()
    {
        $inspections = DB::select("
            SELECT i.*,
                   p.street AS property_street,
                   p.city   AS property_city,
                   s.f_name AS staff_fname,
                   s.l_name AS staff_lname
            FROM inspections i
            LEFT JOIN properties p ON i.property_no = p.property_no
            LEFT JOIN staff s ON i.staff_no = s.staff_no
            ORDER BY i.inspection_date DESC
        ");
        return response()->json(['success' => true, 'data' => $inspections]);
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

        return response()->json(['success' => true, 'message' => 'Inspection recorded'], 201);
    }
}