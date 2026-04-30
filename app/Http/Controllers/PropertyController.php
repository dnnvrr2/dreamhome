<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Owner;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $branch_no  = $request->branch_no;
        $type       = $request->type;
        $max_rent   = $request->max_rent;
        $status     = $request->status;

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

        if ($branch_no) {
            $sql .= " AND p.branch_no = ?";
            $params[] = $branch_no;
        }

        if ($type) {
            $sql .= " AND p.type = ?";
            $params[] = $type;
        }

        if ($max_rent) {
            $sql .= " AND p.rent <= ?";
            $params[] = $max_rent;
        }

        if ($status !== null && $status !== '') {
            $sql .= " AND p.is_available = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY p.property_no ASC";

        $properties = DB::select($sql, $params);
        $branches   = DB::select("SELECT * FROM branches ORDER BY branch_no");
        
        return view('properties.index', compact('properties', 'branches'));
    }

    public function create()
    {
        $owners   = DB::select("SELECT * FROM owners ORDER BY owner_no");
        $branches = DB::select("SELECT * FROM branches ORDER BY branch_no");
        return view('properties.create', compact('owners', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_no' => 'required|unique:properties|max:5',
            'street'      => 'required|max:60',
            'city'        => 'required|max:30',
            'rent'        => 'required|numeric',
            'rooms'       => 'required|integer',
        ]);

        DB::insert("
            INSERT INTO properties 
                (property_no, street, area, city, postcode, type, rooms, rent, is_available, owner_no, branch_no, created_at, updated_at)
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

        return redirect()->route('properties.index')
            ->with('success', 'Property added successfully.');
    }

    public function edit($property_no)
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

        $property = $property[0] ?? abort(404);
        $owners   = DB::select("SELECT * FROM owners ORDER BY owner_no");
        $branches = DB::select("SELECT * FROM branches ORDER BY branch_no");

        return view('properties.edit', compact('property', 'owners', 'branches'));
    }

    public function update(Request $request, $property_no)
    {
        $request->validate([
            'street' => 'required|max:60',
            'city'   => 'required|max:30',
            'rent'   => 'required|numeric',
            'rooms'  => 'required|integer',
        ]);

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

        return redirect()->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }

    public function destroy($property_no)
    {
        DB::delete("DELETE FROM properties WHERE property_no = ?", [$property_no]);

        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully.');
    }
}