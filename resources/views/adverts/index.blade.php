@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Property Adverts</h2>
    <a href="{{ route('adverts.create') }}" class="btn btn-primary">Add Advert</a>
</div>

<form method="GET" action="{{ route('adverts.index') }}" class="row g-2 mb-4">
    <div class="col-md-4">
        <select name="property_no" class="form-control">
            <option value="">-- All Properties --</option>
            @foreach($properties as $property)
                <option value="{{ $property->property_no }}" {{ request('property_no') == $property->property_no ? 'selected' : '' }}>
                    {{ $property->property_no }} - {{ $property->street }}, {{ $property->city }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="newspaper_id" class="form-control">
            <option value="">-- All Newspapers --</option>
            @foreach($newspapers as $newspaper)
                <option value="{{ $newspaper->id }}" {{ request('newspaper_id') == $newspaper->id ? 'selected' : '' }}>
                    {{ $newspaper->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-dark w-100">Filter</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('adverts.index') }}" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Property</th>
                <th>Newspaper</th>
                <th>Cost</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($adverts as $advert)
            <tr>
                <td>{{ \Carbon\Carbon::parse($advert->advert_date)->format('M d, Y') }}</td>
                <td>
                    <strong>{{ $advert->property_no }}</strong><br>
                    <small>{{ $advert->property_street }}, {{ $advert->property_city }}</small>
                </td>
                <td>{{ $advert->newspaper_name }}</td>
                <td>{{ $advert->cost ? 'GBP ' . number_format($advert->cost, 2) : 'N/A' }}</td>
                <td>{{ $advert->comments ?: 'N/A' }}</td>
                <td>
                    <a href="{{ route('adverts.edit', $advert->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('adverts.destroy', $advert->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this advert?')" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No adverts found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
