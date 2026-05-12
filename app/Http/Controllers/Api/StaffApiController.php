<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StaffApiController extends Controller
{
    public function index()
    {
        $staff = DB::select("
            SELECT s.*, b.city AS branch_city
            FROM staff s
            LEFT JOIN branches b ON s.branch_no = b.branch_no
            ORDER BY s.staff_no
        ");
        return response()->json(['success' => true, 'data' => $staff]);
    }

    public function show($staff_no)
    {
        $staff = DB::select("
            SELECT s.*, b.city AS branch_city
            FROM staff s
            LEFT JOIN branches b ON s.branch_no = b.branch_no
            WHERE s.staff_no = ?
        ", [$staff_no]);

        if (empty($staff)) {
            return response()->json(['message' => 'Staff not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $staff[0]]);
    }
}