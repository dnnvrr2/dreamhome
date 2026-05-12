<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProperties  = DB::select("SELECT COUNT(*) as count FROM properties")[0]->count;
        $availableProperties = DB::select("SELECT COUNT(*) as count FROM properties WHERE is_available = true")[0]->count;
        $totalOwners      = DB::select("SELECT COUNT(*) as count FROM owners")[0]->count;
        $totalBranches    = DB::select("SELECT COUNT(*) as count FROM branches")[0]->count;
        $totalStaff       = DB::select("SELECT COUNT(*) as count FROM staff")[0]->count;
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
            SELECT i.*, p.street, p.city,
                   s.f_name AS staff_fname, s.l_name AS staff_lname
            FROM inspections i
            LEFT JOIN properties p ON i.property_no = p.property_no
            LEFT JOIN staff s ON i.staff_no = s.staff_no
            ORDER BY i.inspection_date DESC
            LIMIT 5
        ");

        return view('dashboard', compact(
            'totalProperties', 'availableProperties',
            'totalOwners', 'totalBranches', 'totalStaff',
            'totalClients', 'totalLeases', 'totalInspections',
            'recentProperties', 'recentInspections'
        ));
    }
}