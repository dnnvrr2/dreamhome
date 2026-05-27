@extends('layouts.app')

@section('content')
<h2>Create Lease</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('leases.store') }}" method="POST">
@csrf
<div class="row g-2">
    <div class="col-md-2">
        <label>Lease No</label>
        <input type="text" class="form-control" value="{{ $leaseNo }}" readonly>
    </div>
    <div class="col-md-3">
        <label>Client</label>
        <select name="client_no" class="form-control" required>
            <option value="">-- Select Client --</option>
            @foreach($clients as $c)
                <option value="{{ $c->client_no }}" {{ old('client_no') == $c->client_no ? 'selected' : '' }}>
                    {{ $c->client_no }} - {{ $c->f_name }} {{ $c->l_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label>Property</label>
        <select name="property_no" class="form-control" required>
            <option value="">-- Select Property --</option>
            @foreach($properties as $p)
                <option value="{{ $p->property_no }}" {{ old('property_no') == $p->property_no ? 'selected' : '' }}>
                    {{ $p->property_no }} - {{ $p->street }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label>Staff (Arranged By)</label>
        <select name="staff_no" class="form-control">
            <option value="">-- Select Staff --</option>
            @foreach($staff as $s)
                <option value="{{ $s->staff_no }}" {{ old('staff_no') == $s->staff_no ? 'selected' : '' }}>
                    {{ $s->staff_no }} - {{ $s->f_name }} {{ $s->l_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mt-2">
        <label>Monthly Rent (£)</label>
        <input type="number" name="monthly_rent" step="0.01" class="form-control" value="{{ old('monthly_rent') }}" required>
    </div>
    <div class="col-md-3 mt-2">
        <label>Payment Method</label>
        <select name="payment_method" class="form-control">
            <option value="">-- Select --</option>
            @foreach(['Cash','Direct Debit','Standing Order','Cheque'] as $method)
                <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mt-2">
        <label>Deposit (£)</label>
        <input type="number" name="deposit" step="0.01" class="form-control" value="{{ old('deposit') }}">
    </div>
    <div class="col-md-3 mt-2">
        <label>Deposit Paid?</label>
        <select name="deposit_paid" class="form-control">
            <option value="0" {{ old('deposit_paid') == '0' ? 'selected' : '' }}>No</option>
            <option value="1" {{ old('deposit_paid') == '1' ? 'selected' : '' }}>Yes</option>
        </select>
    </div>
    <div class="col-md-3 mt-2">
        <label>Start Date</label>
        <input type="date" name="date_start" class="form-control" value="{{ old('date_start') }}" required>
    </div>
    <div class="col-md-3 mt-2">
        <label>End Date</label>
        <input type="date" name="date_end" class="form-control" value="{{ old('date_end') }}" readonly required>
    </div>
    <div class="col-md-3 mt-2">
        <label>Duration (months, 3–12)</label>
        <select name="duration_month" class="form-control" required>
            <option value="">-- Select Months --</option>
            @for($month = 3; $month <= 12; $month++)
                <option value="{{ $month }}" {{ old('duration_month') == $month ? 'selected' : '' }}>
                    {{ $month }} months
                </option>
            @endfor
        </select>
    </div>
</div>
<div class="mt-4">
    <button type="submit" class="btn btn-primary">Save Lease</button>
    <a href="{{ route('leases.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const startInput = document.querySelector('[name="date_start"]');
        const durationInput = document.querySelector('[name="duration_month"]');
        const endInput = document.querySelector('[name="date_end"]');

        const addMonthsNoOverflow = (date, months) => {
            const targetMonth = date.getMonth() + months;
            const lastDayOfTargetMonth = new Date(date.getFullYear(), targetMonth + 1, 0).getDate();
            const targetDay = Math.min(date.getDate(), lastDayOfTargetMonth);

            return new Date(date.getFullYear(), targetMonth, targetDay);
        };

        const calculateEndDate = () => {
            if (!startInput.value || !durationInput.value) {
                endInput.value = '';
                return;
            }

            const startDate = new Date(`${startInput.value}T00:00:00`);
            const endDate = addMonthsNoOverflow(startDate, Number(durationInput.value));
            endDate.setDate(endDate.getDate() - 1);
            endInput.value = endDate.toISOString().slice(0, 10);
        };

        startInput.addEventListener('change', calculateEndDate);
        durationInput.addEventListener('change', calculateEndDate);
        calculateEndDate();
    });
</script>
@endsection
