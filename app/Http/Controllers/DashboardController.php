<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProperties  = array_sum(array_map(
            fn ($branch) => (int) DB::selectOne("SELECT fn_count_properties_by_branch(?::varchar) AS count", [$branch->branch_no])->count,
            DB::select("SELECT branch_no FROM branches")
        ));
        $availableProperties = DB::select("SELECT COUNT(*) as count FROM vw_available_properties")[0]->count;
        $totalOwners      = DB::select("SELECT COUNT(*) as count FROM owners")[0]->count;
        $totalBranches    = DB::select("SELECT COUNT(*) as count FROM branches")[0]->count;
        $totalStaff       = array_sum(array_map(
            fn ($position) => (int) DB::selectOne("SELECT fn_count_staff_by_position(?::varchar) AS count", [$position->position])->count,
            DB::select("SELECT DISTINCT position FROM staff")
        ));
        $totalClients     = DB::select("SELECT COUNT(*) as count FROM clients")[0]->count;
        $totalLeases      = DB::select("SELECT COUNT(*) as count FROM leases")[0]->count;
        $totalInspections = DB::select("SELECT COUNT(*) as count FROM inspections")[0]->count;

        $recentProperties = DB::select("
            SELECT p.*, o.f_name AS owner_fname, o.l_name AS owner_lname
            FROM properties p
            LEFT JOIN owners o ON p.owner_no = o.owner_no
            ORDER BY p.created_at DESC
            LIMIT 5
        ");

        $recentInspections = DB::select("
            SELECT inspection_id, inspection_date, comments, property_no, street, city,
                   inspector_fname AS staff_fname,
                   inspector_lname AS staff_lname
            FROM vw_inspection_report
            ORDER BY inspection_date DESC
            LIMIT 5
        ");

        $branchSummaries = array_map(function ($branch) {
            $branch->total_rent = DB::selectOne(
                "SELECT fn_total_rent_by_branch(?::varchar) AS total_rent",
                [$branch->branch_no]
            )->total_rent;

            return $branch;
        }, DB::select("SELECT * FROM vw_branch_summary ORDER BY branch_no"));

        return view('dashboard', compact(
            'totalProperties', 'availableProperties',
            'totalOwners', 'totalBranches', 'totalStaff',
            'totalClients', 'totalLeases', 'totalInspections',
            'recentProperties', 'recentInspections', 'branchSummaries'
        ));
    }
}
