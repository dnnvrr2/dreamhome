<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_no' => 'required|unique:branches|max:4',
            'street'    => 'required|max:60',
            'city'      => 'required|max:30',
            'postcode'  => 'required|max:10',
        ]);

        Branch::create($request->all());
        return redirect()->route('branches.index')->with('success', 'Branch added successfully.');
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'street'   => 'required|max:60',
            'city'     => 'required|max:30',
            'postcode' => 'required|max:10',
        ]);

        $branch->update($request->all());
        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully.');
    }
}