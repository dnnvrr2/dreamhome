@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Properties</h2>
    <a href="{{ route('properties.create') }}" class="btn btn-primary">Add Property</a>
</div>

{{-- Filter Form --}}
<form method="GET" action="{{ route('properties.index') }}" class="row g-2 mb-4">
    <div class="col-md-3">
        <select name="branch_no" class="form-control">
            <option value="">-- All Branches --</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->branch_no }}" 
                    {{ request('branch_no') == $branch->branch_no ? 'selected' : '' }}>
                    {{ $branch->branch_no }} - {{ $branch->city }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="type" class="form-control">
            <option value="">-- All Types --</option>
            @foreach(['Flat','House','Studio','Detached'] as $type)
                <option value="{{ $type }}" 
                    {{ request('type') == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <input type="number" name="max_rent" class="form-control" 
               placeholder="Max Rent" value="{{ request('max_rent') }}">
    </div>
    <div class="col-md-2">
        <select name="staff_no" class="form-control">
            <option value="">-- All Staff --</option>
            @foreach($staff as $s)
                <option value="{{ $s->staff_no }}" {{ request('staff_no') == $s->staff_no ? 'selected' : '' }}>
                    {{ $s->staff_no }} - {{ $s->f_name }} {{ $s->l_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-control">
            <option value="">-- All Status --</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Available</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Withdrawn</option>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-dark w-100">Filter</button>
    </div>
    <div class="col-md-1">
        <a href="{{ route('properties.index') }}" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

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
            <th>Managed By</th>
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
            <td>{{ $property->owner_fname ?? 'N/A' }} {{ $property->owner_lname ?? '' }}</td>
            <td>{{ $property->branch_city ?? 'N/A' }}</td>
            <td>{{ $property->staff_fname ?? 'N/A' }} {{ $property->staff_lname ?? '' }}</td>
            <td>
                @if($property->is_available)
                    <span class="badge bg-success">Available</span>
                @else
                    <span class="badge bg-danger">Withdrawn</span>
                @endif
            </td>
            <td>
                <a href="{{ route('properties.edit', $property->property_no) }}" 
                   class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('properties.destroy', $property->property_no) }}" 
                      method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this property?')" 
                            class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="11" class="text-center">No properties found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
