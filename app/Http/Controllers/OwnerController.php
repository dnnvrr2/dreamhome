<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = DB::select("SELECT * FROM owners ORDER BY owner_no");
        return view('owners.index', compact('owners'));
    }

    public function create()
    {
        return view('owners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_no' => 'required|unique:owners|max:5',
            'f_name'   => 'required|max:30',
            'l_name'   => 'required|max:30',
        ]);

        DB::insert("
            INSERT INTO owners
                (owner_no, f_name, l_name, street, city, postcode, tel_no, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $request->owner_no,
            $request->f_name,
            $request->l_name,
            $request->street,
            $request->city,
            $request->postcode,
            $request->tel_no,
        ]);

        return redirect()->route('owners.index')
            ->with('success', 'Owner added successfully.');
    }

    public function edit($owner_no)
    {
        $owner = DB::select("
            SELECT * FROM owners WHERE owner_no = ?
        ", [$owner_no]);

        $owner = $owner[0] ?? abort(404);

        return view('owners.edit', compact('owner'));
    }

    public function update(Request $request, $owner_no)
    {
        $request->validate([
            'f_name' => 'required|max:30',
            'l_name' => 'required|max:30',
        ]);

        DB::update("
            UPDATE owners
            SET f_name     = ?,
                l_name     = ?,
                street     = ?,
                city       = ?,
                postcode   = ?,
                tel_no     = ?,
                updated_at = NOW()
            WHERE owner_no = ?
        ", [
            $request->f_name,
            $request->l_name,
            $request->street,
            $request->city,
            $request->postcode,
            $request->tel_no,
            $owner_no,
        ]);

        return redirect()->route('owners.index')
            ->with('success', 'Owner updated successfully.');
    }

    public function destroy($owner_no)
    {
        DB::delete("DELETE FROM owners WHERE owner_no = ?", [$owner_no]);

        return redirect()->route('owners.index')
            ->with('success', 'Owner deleted successfully.');
    }
}