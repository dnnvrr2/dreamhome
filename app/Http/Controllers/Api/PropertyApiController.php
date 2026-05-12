<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyApiController extends Controller
{
    public function index(Request $request)
    {
        $sql = "
            SELECT p.*,
                   o.f_name AS owner_fname,
                   o.l_name AS owner_lname,
                   b.city   AS branch_city
            FROM properties p
            LEFT JOIN owners o ON p.owner_no = o.owner_no
            LEFT JOIN branches b ON p.branch_no = b.branch_no
            WHERE 1=1
        ";

        $params = [];

        if ($request->type) {
            $sql .= " AND p.type = ?";
            $params[] = $request->type;
        }

        if ($request->max_rent) {
            $sql .= " AND p.rent <= ?";
            $params[] = $request->max_rent;
        }

        if ($request->status !== null && $request->status !== '') {
            $sql .= " AND p.is_available = ?";
            $params[] = $request->status;
        }

        $sql .= " ORDER BY p.property_no ASC";

        $properties = DB::select($sql, $params);

        return response()->json([
            'success' => true,
            'data'    => $properties
        ]);
    }

    public function show($property_no)
    {
        $property = DB::select("
            SELECT p.*,
                   o.f_name AS owner_fname,
                   o.l_name AS owner_lname,
                   b.city   AS branch_city
            FROM properties p
            LEFT JOIN owners o ON p.owner_no = o.owner_no
            LEFT JOIN branches b ON p.branch_no = b.branch_no
            WHERE p.property_no = ?
        ", [$property_no]);

        if (empty($property)) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $property[0]]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_no' => 'required|max:5|unique:properties',
            'street'      => 'required',
            'city'        => 'required',
            'rent'        => 'required|numeric',
            'rooms'       => 'required|integer',
        ]);

        DB::insert("
            INSERT INTO properties
                (property_no, street, area, city, postcode, type, rooms, rent,
                 is_available, owner_no, branch_no, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $request->property_no,
            $request->street,
            $request->area,
            $request->city,
            $request->postcode,
            $request->type,
            $request->rooms,
            $request->rent,
            $request->is_available ?? true,
            $request->owner_no,
            $request->branch_no,
        ]);

        return response()->json(['success' => true, 'message' => 'Property created'], 201);
    }

    public function update(Request $request, $property_no)
    {
        DB::update("
            UPDATE properties
            SET street       = ?,
                area         = ?,
                city         = ?,
                postcode     = ?,
                type         = ?,
                rooms        = ?,
                rent         = ?,
                is_available = ?,
                owner_no     = ?,
                branch_no    = ?,
                updated_at   = NOW()
            WHERE property_no = ?
        ", [
            $request->street,
            $request->area,
            $request->city,
            $request->postcode,
            $request->type,
            $request->rooms,
            $request->rent,
            $request->is_available ?? true,
            $request->owner_no,
            $request->branch_no,
            $property_no,
        ]);

        return response()->json(['success' => true, 'message' => 'Property updated']);
    }

    public function destroy($property_no)
    {
        DB::delete("DELETE FROM properties WHERE property_no = ?", [$property_no]);
        return response()->json(['success' => true, 'message' => 'Property deleted']);
    }
}