@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Viewings</h2>
    <a href="{{ route('viewings.create') }}" class="btn btn-primary">Schedule Viewing</a>
</div>

<form method="GET" action="{{ route('viewings.index') }}" class="row g-2 mb-4">
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
        <select name="client_no" class="form-control">
            <option value="">-- All Clients --</option>
            @foreach($clients as $c)
                <option value="{{ $c->client_no }}" {{ request('client_no') == $c->client_no ? 'selected' : '' }}>
                    {{ $c->client_no }} - {{ $c->f_name }} {{ $c->l_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-dark w-100">Filter</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('viewings.index') }}" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Client</th>
            <th>Property</th>
            <th>View Date</th>
            <th>Staff</th>
            <th>Comments</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($viewings as $v)
        <tr>
            <td>{{ $v->client_fname }} {{ $v->client_lname }}</td>
            <td>{{ $v->property_street }}, {{ $v->property_city }}</td>
            <td>{{ $v->view_date }}</td>
            <td>{{ $v->staff_fname ?? 'N/A' }} {{ $v->staff_lname ?? '' }}</td>
            <td>{{ $v->comments ?? 'N/A' }}</td>
            <td>
                <a href="{{ route('viewings.edit', $v->client_no) }}?property_no={{ $v->property_no }}&view_date={{ $v->view_date }}"
                   class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('viewings.destroy', $v->client_no) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <input type="hidden" name="property_no" value="{{ $v->property_no }}">
                    <input type="hidden" name="view_date" value="{{ $v->view_date }}">
                    <button onclick="return confirm('Delete this viewing?')"
                            class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No viewings found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection