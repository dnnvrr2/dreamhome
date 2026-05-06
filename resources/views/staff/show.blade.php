@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Staff Detail — {{ $staff->staff_no }}</h2>
    <div>
        <a href="{{ route('staff.edit', $staff->staff_no) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('staff.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-dark text-white">Basic Information</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>Staff No:</strong> {{ $staff->staff_no }}</div>
            <div class="col-md-3"><strong>Name:</strong> {{ $staff->f_name }} {{ $staff->l_name }}</div>
            <div class="col-md-3"><strong>Position:</strong> {{ $staff->position }}</div>
            <div class="col-md-3"><strong>Sex:</strong> {{ $staff->sex ?? 'N/A' }}</div>
            <div class="col-md-3 mt-2"><strong>DOB:</strong> {{ $staff->dob ?? 'N/A' }}</div>
            <div class="col-md-3 mt-2"><strong>NIN:</strong> {{ $staff->nin ?? 'N/A' }}</div>
            <div class="col-md-3 mt-2"><strong>Salary:</strong> £{{ number_format($staff->salary, 2) }}</div>
            <div class="col-md-3 mt-2"><strong>Date Joined:</strong> {{ $staff->date_joined ?? 'N/A' }}</div>
            <div class="col-md-3 mt-2"><strong>Branch:</strong> {{ $staff->branch_city ?? 'N/A' }}</div>
            <div class="col-md-3 mt-2"><strong>Supervisor:</strong>
                {{ $staff->sup_fname ? $staff->sup_fname . ' ' . $staff->sup_lname : 'None' }}
            </div>
        </div>
    </div>
</div>

@if($manager)
<div class="card mb-3">
    <div class="card-header bg-primary text-white">Manager Details</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Date Started:</strong> {{ $manager->date_start ?? 'N/A' }}</div>
            <div class="col-md-4"><strong>Car Allowance:</strong> £{{ number_format($manager->car_allowance, 2) }}</div>
            <div class="col-md-4"><strong>Monthly Bonus:</strong> £{{ number_format($manager->bonus, 2) }}</div>
        </div>
    </div>
</div>
@endif

@if($secretary)
<div class="card mb-3">
    <div class="card-header bg-info text-white">Secretary Details</div>
    <div class="card-body">
        <strong>Typing Speed:</strong> {{ $secretary->typing_speed }} WPM
    </div>
</div>
@endif

@if($kin)
<div class="card mb-3">
    <div class="card-header bg-secondary text-white">Next of Kin</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>Name:</strong> {{ $kin->full_name }}</div>
            <div class="col-md-3"><strong>Relationship:</strong> {{ $kin->relationship ?? 'N/A' }}</div>
            <div class="col-md-3"><strong>Street:</strong> {{ $kin->street ?? 'N/A' }}</div>
            <div class="col-md-3"><strong>City:</strong> {{ $kin->city ?? 'N/A' }}</div>
            <div class="col-md-3 mt-2"><strong>Tel No:</strong> {{ $kin->tel_no ?? 'N/A' }}</div>
        </div>
    </div>
</div>
@endif
@endsection