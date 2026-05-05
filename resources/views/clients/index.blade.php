@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Clients</h2>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">Register Client</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="GET" action="{{ route('clients.index') }}" class="row g-2 mb-4">
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
        <select name="pref_type" class="form-control">
            <option value="">-- All Types --</option>
            @foreach(['Flat','House','Studio','Detached'] as $type)
                <option value="{{ $type }}"
                    {{ request('pref_type') == $type ? 'selected' : '' }}>
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
        <select name="registered_by" class="form-control">
            <option value="">-- All Staff --</option>
            @foreach($staff as $s)
                <option value="{{ $s->staff_no }}"
                    {{ request('registered_by') == $s->staff_no ? 'selected' : '' }}>
                    {{ $s->f_name }} {{ $s->l_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-dark w-100">Filter</button>
    </div>
    <div class="col-md-1">
        <a href="{{ route('clients.index') }}" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Client No</th>
            <th>Name</th>
            <th>City</th>
            <th>Tel No</th>
            <th>Pref Type</th>
            <th>Max Rent</th>
            <th>Branch</th>
            <th>Registered By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $client)
        <tr>
            <td>{{ $client->client_no }}</td>
            <td>{{ $client->f_name }} {{ $client->l_name }}</td>
            <td>{{ $client->city ?? 'N/A' }}</td>
            <td>{{ $client->tel_no ?? 'N/A' }}</td>
            <td>{{ $client->pref_type ?? 'N/A' }}</td>
            <td>£{{ number_format($client->max_rent, 2) }}</td>
            <td>{{ $client->branch_city ?? 'N/A' }}</td>
            <td>{{ $client->staff_fname ?? 'N/A' }} {{ $client->staff_lname ?? '' }}</td>
            <td>
                <a href="{{ route('clients.edit', $client->client_no) }}"
                   class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('clients.destroy', $client->client_no) }}"
                      method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this client?')"
                            class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center">No clients found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection