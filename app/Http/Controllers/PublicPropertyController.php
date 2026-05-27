<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicPropertyController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;
        $city = $request->city;
        $maxRent = $request->max_rent;
        $rooms = $request->rooms;

        $sql = "
            SELECT v.*, true AS is_available
            FROM vw_available_properties v
            WHERE 1=1
        ";

        $params = [];

        if ($type) {
            $sql .= " AND v.type = ?";
            $params[] = $type;
        }

        if ($city) {
            $sql .= " AND v.city LIKE ?";
            $params[] = '%' . $city . '%';
        }

        if ($maxRent) {
            $sql .= " AND v.rent <= ?";
            $params[] = $maxRent;
        }

        if ($rooms) {
            $sql .= " AND v.rooms >= ?";
            $params[] = $rooms;
        }

        $sql .= " ORDER BY v.rent ASC, v.property_no ASC";

        $properties = DB::select($sql, $params);
        $types = DB::select("
            SELECT DISTINCT type
            FROM vw_available_properties
            WHERE type IS NOT NULL AND type <> ''
            ORDER BY type
        ");

        return view('public.properties', compact('properties', 'types'));
    }
}
