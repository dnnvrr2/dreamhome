@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Properties</h2>
    <a href="{{ route('properties.create') }}" class="btn btn-primary">Add Property</a>
</div>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Property No</th>
            <th>Street</th>
            <th>City</th>
            <th>Type</th>
            <th>Rooms</th>
            <th>Rent</th>
            <th>Owner</th>
            <th>Branch</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($properties as $property)
        <tr>
            <td>{{ $property->property_no }}</td>
            <td>{{ $property->street }}</td>
            <td>{{ $property->city }}</td>
            <td>{{ $property->type }}</td>
            <td>{{ $property->rooms }}</td>
            <td>£{{ number_format($property->rent, 2) }}</td>
            <td>{{ $property->owner ? $property->owner->f_name . ' ' . $property->owner->l_name : 'N/A' }}</td>
            <td>{{ $property->branch ? $property->branch->city : 'N/A' }}</td>
            <td>
                @if($property->is_available)
                    <span class="badge bg-success">Available</span>
                @else
                    <span class="badge bg-danger">Withdrawn</span>
                @endif
            </td>
            <td>
                <a href="{{ route('properties.edit', $property->property_no) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('properties.destroy', $property->property_no) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this property?')" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center">No properties found.</td></tr>
        @endforelse
    </tbody>
</table>
@end