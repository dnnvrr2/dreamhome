<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Owner;
use App\Models\Branch;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::with(['owner', 'branch'])->get();
        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        $owners = Owner::all();
        $branches = Branch::all();
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

        Property::create($request->all());
        return redirect()->route('properties.index')->with('success', 'Property added successfully.');
    }

    public function edit(Property $property)
    {
        $owners = Owner::all();
        $branches = Branch::all();
        return view('properties.edit', compact('property', 'owners', 'branches'));
    }

    public function update(Request $request, Property $property)
    {
        $request->validate([
            'street' => 'required|max:60',
            'city'   => 'required|max:30',
            'rent'   => 'required|numeric',
            'rooms'  => 'required|integer',
        ]);

        $property->update($request->all());
        return redirect()->route('properties.index')->with('success', 'Property updated successfully.');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('properties.index')->with('success', 'Property deleted successfully.');
    }
}