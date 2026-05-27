@extends('layouts.app')

@section('content')
<h2 class="mb-4">Register Client</h2>

<form action="{{ route('clients.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-md-2">
            <label>Client No</label>
            <input type="text" class="form-control" value="{{ $clientNo }}" readonly>
        </div>
        <div class="col-md-5">
            <label>First Name</label>
            <input type="text" name="f_name" class="form-control" required>
        </div>
        <div class="col-md-5">
            <label>Last Name</label>
            <input type="text" name="l_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Street</label>
            <input type="text" name="street" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Area</label>
            <input type="text" name="area" class="form-control">
        </div>
        <div class="col-md-3">
            <label>City</label>
            <input type="text" name="city" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Postcode</label>
            <input type="text" name="postcode" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Tel No</label>
            <input type="text" name="tel_no" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Preferred Type</label>
            <select name="pref_type" class="form-control">
                <option value="">-- Select --</option>
                @foreach(['Flat','House','Studio','Detached'] as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Max Rent (£)</label>
            <input type="number" name="max_rent" class="form-control" step="0.01">
        </div>
        <div class="col-md-6">
            <label>Comments</label>
            <textarea name="comments" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-md-4">
            <label>Branch</label>
            <select name="branch_no" class="form-control">
                <option value="">-- Select Branch --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->branch_no }}">
                        {{ $branch->branch_no }} - {{ $branch->city }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label>Assigned Staff</label>
            <select name="registered_by" class="form-control">
                <option value="">-- Select Staff --</option>
                @foreach($staff as $s)
                    <option value="{{ $s->staff_no }}">
                        {{ $s->f_name }} {{ $s->l_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Register Client</button>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
