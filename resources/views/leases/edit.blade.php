@extends('layouts.app')

@section('content')
<h2>Edit Lease — {{ $lease->lease_no }}</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('leases.update', $lease->lease_no) }}" method="POST">
@csrf @method('PUT')
<div class="row g-2">
    <div class="col-md-3">
        <label>Client</label>
        <select name="client_no" class="form-control" required>
            @foreach($clients as $c)
                <option value="{{ $c->client_no }}" {{ $lease->client_no == $c->client_no ? 'selected' : '' }}>
                    {{ $c->client_no }} - {{ $c->f_name }} {{ $c->l_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label>Property</label>
        <select name="property_no" class="form-control" required>
            @foreach($properties as $p)
                <option value="{{ $p->property_no }}" {{ $lease->property_no == $p->property_no ? 'selected' : '' }}>
                    {{ $p->property_no }} - {{ $p->street }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label>Staff</label>
        <select name="staff_no" class="form-control">
            <option value="">-- None --</option>
            @foreach($staff as $s)
                <option value="{{ $s->staff_no }}" {{ $lease->staff_no == $s->staff_no ? 'selected' : '' }}>
                    {{ $s->staff_no }} - {{ $s->f_name }} {{ $s->l_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label>Monthly Rent (£)</label>
        <input type="number" name="monthly_rent" step="0.01" class="form-control" value="{{ old('monthly_rent', $lease->monthly_rent) }}" required>
    </div>
    <div class="col-md-3 mt-2">
        <label>Payment Method</label>
        <select name="payment_method" class="form-control">
            @foreach(['Cash','Direct Debit','Standing Order','Cheque'] as $method)
                <option value="{{ $method }}" {{ $lease->payment_method == $method ? 'selected' : '' }}>{{ $method }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mt-2">
        <label>Deposit (£)</label>
        <input type="number" name="deposit" step="0.01" class="form-control" value="{{ old('deposit', $lease->deposit) }}">
    </div>
    <div class="col-md-3 mt-2">
        <label>Deposit Paid?</label>
        <select name="deposit_paid" class="form-control">
            <option value="0" {{ !$lease->deposit_paid ? 'selected' : '' }}>No</option>
            <option value="1" {{ $lease->deposit_paid ? 'selected' : '' }}>Yes</option>
        </select>
    </div>
    <div class="col-md-3 mt-2">
        <label>Start Date</label>
        <input type="date" name="date_start" class="form-control" value="{{ old('date_start', $lease->date_start) }}" required>
    </div>
    <div class="col-md-3 mt-2">
        <label>End Date</label>
        <input type="date" name="date_end" class="form-control" value="{{ old('date_end', $lease->date_end) }}" required>
    </div>
    <div class="col-md-3 mt-2">
        <label>Duration (months)</label>
        <input type="number" name="duration_month" class="form-control" min="3" max="12" value="{{ old('duration_month', $lease->duration_month) }}" required>
    </div>
</div>
<div class="mt-4">
    <button type="submit" class="btn btn-primary">Update Lease</button>
    <a href="{{ route('leases.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>
@endsection