@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Leases</h2>
    <a href="{{ route('leases.create') }}" class="btn btn-primary">Create Lease</a>
</div>

<form method="GET" action="{{ route('leases.index') }}" class="row g-2 mb-4">
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
    <div class="col-md-2">
        <button type="submit" class="btn btn-dark w-100">Filter</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('leases.index') }}" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Lease No</th>
            <th>Client</th>
            <th>Property</th>
            <th>Monthly Rent</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Duration</th>
            <th>Deposit Paid</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($leases as $l)
        <tr>
            <td>{{ $l->lease_no }}</td>
            <td>{{ $l->client_fname }} {{ $l->client_lname }}</td>
            <td>{{ $l->property_street }}, {{ $l->property_city }}</td>
            <td>£{{ number_format($l->monthly_rent, 2) }}</td>
            <td>{{ $l->date_start }}</td>
            <td>{{ $l->date_end }}</td>
            <td>{{ $l->duration_month }} months</td>
            <td>
                @if($l->deposit_paid)
                    <span class="badge bg-success">Yes</span>
                @else
                    <span class="badge bg-danger">No</span>
                @endif
            </td>
            <td>
                <a href="{{ route('leases.show', $l->lease_no) }}" class="btn btn-sm btn-info">View</a>
                <a href="{{ route('leases.edit', $l->lease_no) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('leases.destroy', $l->lease_no) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this lease?')"
                            class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center">No leases found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection