<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Owner;
use App\Models\Branch;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $branch_no  = $request->branch_no;
        $staff_no   = $request->staff_no;
        $type       = $request->type;
        $max_rent   = $request->max_rent;
        $status     = $request->status;

        $usesAvailableView = $status === '1';
        $propertyAlias = $usesAvailableView ? 'v' : 'p';

        $sql = $usesAvailableView
            ? "
                SELECT v.*, true AS is_available
                FROM vw_available_properties v
                WHERE 1=1
            "
            : "
                SELECT p.*,
                    o.f_name AS owner_fname,
                    o.l_name AS owner_lname,
                    b.city AS branch_city,
                    s.f_name AS staff_fname,
                    s.l_name AS staff_lname
                FROM properties p
                LEFT JOIN owners o ON p.owner_no = o.owner_no
                LEFT JOIN branches b ON p.branch_no = b.branch_no
                LEFT JOIN staff s ON p.staff_no = s.staff_no
                WHERE 1=1
            ";

        $params = [];

        if ($branch_no) {
            $sql .= " AND {$propertyAlias}.branch_no = ?";
            $params[] = $branch_no;
        }

        if ($staff_no) {
            $sql .= " AND {$propertyAlias}.staff_no = ?";
            $params[] = $staff_no;
        }

        if ($type) {
            $sql .= " AND {$propertyAlias}.type = ?";
            $params[] = $type;
        }

        if ($max_rent) {
            $sql .= " AND {$propertyAlias}.rent <= ?";
            $params[] = $max_rent;
        }

        if ($status !== null && $status !== '') {
            if (!$usesAvailableView) {
                $sql .= " AND p.is_available = ?";
                $params[] = $status;
            }
        }

        $sql .= " ORDER BY property_no ASC";

        $properties = DB::select($sql, $params);
        $branches   = DB::select("SELECT * FROM branches ORDER BY branch_no");
        $staff      = DB::select("SELECT * FROM staff ORDER BY staff_no");
        
        return view('properties.index', compact('properties', 'branches', 'staff'));
    }

    public function create()
    {
        $owners   = DB::select("SELECT * FROM owners ORDER BY owner_no");
        $branches = DB::select("SELECT * FROM branches ORDER BY branch_no");
        $staff    = DB::select("SELECT * FROM staff ORDER BY staff_no");
        $branchPropertyNos = [];

        foreach ($branches as $branch) {
            $branchPropertyNos[$branch->branch_no] = $this->nextPropertyNoForBranch($branch->branch_no);
        }

        return view('properties.create', compact('owners', 'branches', 'staff', 'branchPropertyNos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'street'      => 'required|max:60',
            'city'        => 'required|max:30',
            'rent'        => 'required|numeric|min:100',
            'rooms'       => 'required|integer|min:1',
            'branch_no'   => 'required|exists:branches,branch_no',
            'staff_no'    => 'nullable|exists:staff,staff_no',
        ]);

        $propertyNo = $this->nextPropertyNoForBranch($request->branch_no);

        try {
            DB::transaction(function () use ($request, $propertyNo) {
                DB::statement("
                    CALL sp_add_property(
                        ?::varchar, ?::varchar, ?::varchar, ?::varchar,
                        ?::smallint, ?::numeric, ?::varchar, ?::varchar, ?::varchar
                    )
                ", [
                    $propertyNo,
                    $request->street,
                    $request->city,
                    $request->type,
                    $request->rooms,
                    $request->rent,
                    $request->owner_no,
                    $request->branch_no,
                    $request->staff_no,
                ]);

                DB::update("
                    UPDATE properties
                    SET area = ?,
                        postcode = ?,
                        is_available = ?,
                        updated_at = NOW()
                    WHERE property_no = ?
                ", [
                    $request->area,
                    $request->postcode,
                    $request->is_available ?? true,
                    $propertyNo,
                ]);
            });
        } catch (QueryException $exception) {
            return back()->withInput()->with('error', $this->databaseError($exception));
        }

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
        $staff    = DB::select("SELECT * FROM staff ORDER BY staff_no");

        return view('properties.edit', compact('property', 'owners', 'branches', 'staff'));
    }

    public function update(Request $request, $property_no)
    {
        $request->validate([
            'street' => 'required|max:60',
            'city'   => 'required|max:30',
            'rent'   => 'required|numeric|min:100',
            'rooms'  => 'required|integer|min:1',
            'staff_no' => 'nullable|exists:staff,staff_no',
        ]);

        try {
            DB::transaction(function () use ($request, $property_no) {
                DB::statement("CALL sp_update_rent(?::varchar, ?::numeric)", [
                    $property_no,
                    $request->rent,
                ]);

                DB::statement("CALL sp_transfer_property(?::varchar, ?::varchar)", [
                    $property_no,
                    $request->branch_no,
                ]);

                if (!$request->boolean('is_available')) {
                    DB::statement("CALL sp_withdraw_property(?::varchar)", [$property_no]);
                }

                DB::update("
                    UPDATE properties
                    SET street       = ?,
                        area         = ?,
                        city         = ?,
                        postcode     = ?,
                        type         = ?,
                        rooms        = ?,
                        is_available = ?,
                        owner_no     = ?,
                        staff_no     = ?,
                        updated_at   = NOW()
                    WHERE property_no = ?
                ", [
                    $request->street,
                    $request->area,
                    $request->city,
                    $request->postcode,
                    $request->type,
                    $request->rooms,
                    $request->is_available ?? true,
                    $request->owner_no,
                    $request->staff_no,
                    $property_no,
                ]);
            });
        } catch (QueryException $exception) {
            return back()->withInput()->with('error', $this->databaseError($exception));
        }

        return redirect()->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }

    public function destroy($property_no)
    {
        try {
            DB::delete("DELETE FROM properties WHERE property_no = ?", [$property_no]);
        } catch (QueryException $exception) {
            return back()->with('error', $this->databaseError($exception));
        }

        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully.');
    }

    private function nextPropertyNoForBranch(string $branchNo): string
    {
        $branch = DB::selectOne("SELECT city FROM branches WHERE branch_no = ?", [$branchNo]);
        $cityInitial = strtoupper(substr((string) ($branch->city ?? ''), 0, 1)) ?: 'X';

        return $this->nextPrefixedId('properties', 'property_no', 'P' . $cityInitial, 3);
    }
}
