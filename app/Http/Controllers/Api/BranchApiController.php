<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BranchApiController extends Controller
{
    public function index()
    {
        $branches = DB::select("SELECT * FROM branches ORDER BY branch_no");
        return response()->json(['success' => true, 'data' => $branches]);
    }

    public function show($branch_no)
    {
        $branch = DB::select("SELECT * FROM branches WHERE branch_no = ?", [$branch_no]);
        if (empty($branch)) {
            return response()->json(['message' => 'Branch not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $branch[0]]);
    }
}