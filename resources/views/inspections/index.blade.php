@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Inspections</h2>
    <a href="{{ route('inspections.create') }}" class="btn btn-primary">Record Inspection</a>
</div>

<form method="GET" action="{{ route('inspections.index') }}" class="row g-2 mb-4">
    <div class="col-md-3">
        <select name="property_no" class="form-control">
            <option value="">-- All Properties --</option>
            @foreach($properties as $p)
                <option value="{{ $p->property_no }}" {{ request('property_no') == $p->property_no ? 'selected' : '' }}>
                    {{ $p->property_no }} - {{ $p->street }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
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
        <button type="submit" class="btn btn-dark w-100">Filter</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('inspections.index') }}" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Property</th>
            <th>Inspected By</th>
            <th>Date</th>
            <th>Comments</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($inspections as $i)
        <tr>
            <td>{{ $i->inspection_id }}</td>
            <td>{{ $i->property_street }}, {{ $i->property_city }}</td>
            <td>{{ $i->staff_fname ?? 'N/A' }} {{ $i->staff_lname ?? '' }}</td>
            <td>{{ $i->inspection_date }}</td>
            <td>{{ $i->comments ?? 'N/A' }}</td>
            <td>
                <a href="{{ route('inspections.edit', $i->inspection_id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('inspections.destroy', $i->inspection_id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this inspection?')"
                            class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No inspections found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection