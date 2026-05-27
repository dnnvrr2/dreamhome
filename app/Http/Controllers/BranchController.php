<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $branches = DB::select("SELECT * FROM branches ORDER BY branch_no");
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        $branchNo = $this->nextPrefixedId('branches', 'branch_no', 'B', 3);
        return view('branches.create', compact('branchNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'street'    => 'required|max:60',
            'city'      => 'required|max:30',
            'postcode'  => 'required|max:10',
        ]);

        $branchNo = $this->nextPrefixedId('branches', 'branch_no', 'B', 3);

        DB::insert("
            INSERT INTO branches 
                (branch_no, street, area, city, postcode, tel_no, fax_no, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $branchNo,
            $request->street,
            $request->area,
            $request->city,
            $request->postcode,
            $request->tel_no,
            $request->fax_no,
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch added successfully.');
    }

    public function edit($branch_no)
    {
        $branch = DB::select("
            SELECT * FROM branches WHERE branch_no = ?
        ", [$branch_no]);

        $branch = $branch[0] ?? abort(404);

        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, $branch_no)
    {
        $request->validate([
            'street'   => 'required|max:60',
            'city'     => 'required|max:30',
            'postcode' => 'required|max:10',
        ]);

        DB::update("
            UPDATE branches
            SET street     = ?,
                area       = ?,
                city       = ?,
                postcode   = ?,
                tel_no     = ?,
                fax_no     = ?,
                updated_at = NOW()
            WHERE branch_no = ?
        ", [
            $request->street,
            $request->area,
            $request->city,
            $request->postcode,
            $request->tel_no,
            $request->fax_no,
            $branch_no,
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy($branch_no)
    {
        DB::delete("DELETE FROM branches WHERE branch_no = ?", [$branch_no]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch deleted successfully.');
    }
}
