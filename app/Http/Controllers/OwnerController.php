<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::all();
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

        Owner::create($request->all());
        return redirect()->route('owners.index')->with('success', 'Owner added successfully.');
    }

    public function edit(Owner $owner)
    {
        return view('owners.edit', compact('owner'));
    }

    public function update(Request $request, Owner $owner)
    {
        $request->validate([
            'f_name' => 'required|max:30',
            'l_name' => 'required|max:30',
        ]);

        $owner->update($request->all());
        return redirect()->route('owners.index')->with('success', 'Owner updated successfully.');
    }

    public function destroy(Owner $owner)
    {
        $owner->delete();
        return redirect()->route('owners.index')->with('success', 'Owner deleted successfully.');
    }
}