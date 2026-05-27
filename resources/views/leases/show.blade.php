@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Lease Detail — {{ $lease->lease_no }}</h2>
    <div>
        <a href="{{ route('leases.edit', $lease->lease_no) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('leases.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-dark text-white">Lease Information</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>Lease No:</strong> {{ $lease->lease_no }}</div>
            <div class="col-md-3"><strong>Client:</strong> {{ $lease->client_fname }} {{ $lease->client_lname }}</div>
            <div class="col-md-3"><strong>Property:</strong> {{ $lease->property_street }}, {{ $lease->property_city }}</div>
            <div class="col-md-3"><strong>Arranged By:</strong> {{ $lease->staff_fname ?? 'N/A' }} {{ $lease->staff_lname ?? '' }}</div>
            <div class="col-md-3 mt-2"><strong>Monthly Rent:</strong> £{{ number_format($lease->monthly_rent, 2) }}</div>
            <div class="col-md-3 mt-2"><strong>Payment Method:</strong> {{ $lease->payment_method ?? 'N/A' }}</div>
            <div class="col-md-3 mt-2"><strong>Deposit:</strong> £{{ number_format($lease->deposit, 2) }}</div>
            <div class="col-md-3 mt-2"><strong>Deposit Paid:</strong>
                @if($lease->deposit_paid)
                    <span class="badge bg-success">Yes</span>
                @else
                    <span class="badge bg-danger">No</span>
                @endif
            </div>
            <div class="col-md-3 mt-2"><strong>Start Date:</strong> {{ $lease->date_start }}</div>
            <div class="col-md-3 mt-2"><strong>End Date:</strong> {{ $lease->date_end }}</div>
            <div class="col-md-3 mt-2"><strong>Duration:</strong> {{ $lease->duration_month }} months</div>
            <div class="col-md-3 mt-2"><strong>Days:</strong> {{ $lease->duration_days }} days</div>
        </div>
    </div>
</div>
@endsection
