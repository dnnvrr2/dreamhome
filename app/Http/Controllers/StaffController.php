<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $branch_no = $request->branch_no;
        $position  = $request->position;

        $sql = "
            SELECT s.*,
                   b.city AS branch_city,
                   sup.f_name AS sup_fname,
                   sup.l_name AS sup_lname
            FROM staff s
            LEFT JOIN branches b ON s.branch_no = b.branch_no
            LEFT JOIN staff sup ON s.supervisor_no = sup.staff_no
            WHERE 1=1
        ";

        $params = [];

        if ($branch_no) {
            $sql .= " AND s.branch_no = ?";
            $params[] = $branch_no;
        }

        if ($position) {
            $sql .= " AND s.position = ?";
            $params[] = $position;
        }

        $sql .= " ORDER BY s.staff_no ASC";

        $staff    = DB::select($sql, $params);
        $branches = DB::select("SELECT * FROM branches ORDER BY branch_no");

        return view('staff.index', compact('staff', 'branches'));
    }

    public function create()
    {
        $branches    = DB::select("SELECT * FROM branches ORDER BY branch_no");
        $supervisors = DB::select("SELECT * FROM staff WHERE position = 'Supervisor' ORDER BY staff_no");
        return view('staff.create', compact('branches', 'supervisors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_no' => 'required|max:5|unique:staff',
            'f_name'   => 'required|max:30',
            'l_name'   => 'required|max:30',
            'position' => 'required|max:20',
            'salary'   => 'nullable|numeric',
        ]);

        DB::insert("
            INSERT INTO staff
                (staff_no, f_name, l_name, street, area, city, postcode,
                 tel_no, sex, dob, nin, position, salary, date_joined,
                 branch_no, supervisor_no, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $request->staff_no,
            $request->f_name,
            $request->l_name,
            $request->street,
            $request->area,
            $request->city,
            $request->postcode,
            $request->tel_no,
            $request->sex,
            $request->dob,
            $request->nin,
            $request->position,
            $request->salary,
            $request->date_joined,
            $request->branch_no,
            $request->supervisor_no ?: null,
        ]);

        // Insert subtype record based on position
        if ($request->position === 'Manager') {
            DB::insert("
                INSERT INTO managers (staff_no, date_start, car_allowance, bonus, created_at, updated_at)
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ", [
                $request->staff_no,
                $request->date_start,
                $request->car_allowance,
                $request->bonus,
            ]);
        } elseif ($request->position === 'Secretary') {
            DB::insert("
                INSERT INTO secretaries (staff_no, typing_speed, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())
            ", [
                $request->staff_no,
                $request->typing_speed,
            ]);
        } elseif ($request->position === 'Supervisor') {
            DB::insert("
                INSERT INTO supervisors (staff_no, created_at, updated_at)
                VALUES (?, NOW(), NOW())
            ", [$request->staff_no]);
        }

        // Insert next of kin if provided
        if ($request->kin_full_name) {
            DB::insert("
                INSERT INTO next_of_kins
                    (staff_no, full_name, relationship, street, city, tel_no, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $request->staff_no,
                $request->kin_full_name,
                $request->kin_relationship,
                $request->kin_street,
                $request->kin_city,
                $request->kin_tel_no,
            ]);
        }

        return redirect()->route('staff.index')
            ->with('success', 'Staff member added successfully.');
    }

    public function show($staff_no)
    {
        $staff = DB::select("
            SELECT s.*,
                   b.city AS branch_city,
                   sup.f_name AS sup_fname,
                   sup.l_name AS sup_lname
            FROM staff s
            LEFT JOIN branches b ON s.branch_no = b.branch_no
            LEFT JOIN staff sup ON s.supervisor_no = sup.staff_no
            WHERE s.staff_no = ?
        ", [$staff_no]);

        $staff = $staff[0] ?? abort(404);

        $manager   = DB::select("SELECT * FROM managers WHERE staff_no = ?", [$staff_no]);
        $secretary = DB::select("SELECT * FROM secretaries WHERE staff_no = ?", [$staff_no]);
        $kin       = DB::select("SELECT * FROM next_of_kins WHERE staff_no = ?", [$staff_no]);

        $manager   = $manager[0]   ?? null;
        $secretary = $secretary[0] ?? null;
        $kin       = $kin[0]       ?? null;

        return view('staff.show', compact('staff', 'manager', 'secretary', 'kin'));
    }

    public function edit($staff_no)
    {
        $staff = DB::select("SELECT * FROM staff WHERE staff_no = ?", [$staff_no]);
        $staff = $staff[0] ?? abort(404);

        $branches    = DB::select("SELECT * FROM branches ORDER BY branch_no");
        $supervisors = DB::select("SELECT * FROM staff WHERE position = 'Supervisor' AND staff_no != ? ORDER BY staff_no", [$staff_no]);

        $manager   = DB::select("SELECT * FROM managers WHERE staff_no = ?", [$staff_no]);
        $secretary = DB::select("SELECT * FROM secretaries WHERE staff_no = ?", [$staff_no]);
        $kin       = DB::select("SELECT * FROM next_of_kins WHERE staff_no = ?", [$staff_no]);

        $manager   = $manager[0]   ?? null;
        $secretary = $secretary[0] ?? null;
        $kin       = $kin[0]       ?? null;

        return view('staff.edit', compact('staff', 'branches', 'supervisors', 'manager', 'secretary', 'kin'));
    }

    public function update(Request $request, $staff_no)
    {
        $request->validate([
            'f_name'   => 'required|max:30',
            'l_name'   => 'required|max:30',
            'position' => 'required|max:20',
            'salary'   => 'nullable|numeric',
        ]);

        DB::update("
            UPDATE staff
            SET f_name        = ?,
                l_name        = ?,
                street        = ?,
                area          = ?,
                city          = ?,
                postcode      = ?,
                tel_no        = ?,
                sex           = ?,
                dob           = ?,
                nin           = ?,
                position      = ?,
                salary        = ?,
                date_joined   = ?,
                branch_no     = ?,
                supervisor_no = ?,
                updated_at    = NOW()
            WHERE staff_no = ?
        ", [
            $request->f_name,
            $request->l_name,
            $request->street,
            $request->area,
            $request->city,
            $request->postcode,
            $request->tel_no,
            $request->sex,
            $request->dob,
            $request->nin,
            $request->position,
            $request->salary,
            $request->date_joined,
            $request->branch_no,
            $request->supervisor_no ?: null,
            $staff_no,
        ]);

        // Update subtype
        if ($request->position === 'Manager') {
            $exists = DB::select("SELECT * FROM managers WHERE staff_no = ?", [$staff_no]);
            if ($exists) {
                DB::update("UPDATE managers SET date_start=?, car_allowance=?, bonus=?, updated_at=NOW() WHERE staff_no=?",
                    [$request->date_start, $request->car_allowance, $request->bonus, $staff_no]);
            } else {
                DB::insert("INSERT INTO managers (staff_no, date_start, car_allowance, bonus, created_at, updated_at) VALUES (?,?,?,?,NOW(),NOW())",
                    [$staff_no, $request->date_start, $request->car_allowance, $request->bonus]);
            }
        } elseif ($request->position === 'Secretary') {
            $exists = DB::select("SELECT * FROM secretaries WHERE staff_no = ?", [$staff_no]);
            if ($exists) {
                DB::update("UPDATE secretaries SET typing_speed=?, updated_at=NOW() WHERE staff_no=?",
                    [$request->typing_speed, $staff_no]);
            } else {
                DB::insert("INSERT INTO secretaries (staff_no, typing_speed, created_at, updated_at) VALUES (?,?,NOW(),NOW())",
                    [$staff_no, $request->typing_speed]);
            }
        }

        // Update next of kin
        if ($request->kin_full_name) {
            $kinExists = DB::select("SELECT * FROM next_of_kins WHERE staff_no = ?", [$staff_no]);
            if ($kinExists) {
                DB::update("
                    UPDATE next_of_kins
                    SET full_name=?, relationship=?, street=?, city=?, tel_no=?, updated_at=NOW()
                    WHERE staff_no=?
                ", [$request->kin_full_name, $request->kin_relationship, $request->kin_street, $request->kin_city, $request->kin_tel_no, $staff_no]);
            } else {
                DB::insert("
                    INSERT INTO next_of_kins (staff_no, full_name, relationship, street, city, tel_no, created_at, updated_at)
                    VALUES (?,?,?,?,?,?,NOW(),NOW())
                ", [$staff_no, $request->kin_full_name, $request->kin_relationship, $request->kin_street, $request->kin_city, $request->kin_tel_no]);
            }
        }

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy($staff_no)
    {
        DB::delete("DELETE FROM staff WHERE staff_no = ?", [$staff_no]);
        return redirect()->route('staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }
}