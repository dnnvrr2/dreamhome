@extends('layouts.app')

@section('content')
<h2>Add Property</h2>
<form action="{{ route('properties.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label>Property No</label>
                <input type="text" name="property_no" class="form-control" maxlength="5" required>
            </div>
            <div class="mb-3">
                <label>Street</label>
                <input type="text" name="street" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Area</label>
                <input type="text" name="area" class="form-control">
            </div>
            <div class="mb-3">
                <label>City</label>
                <input type="text" name="city" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Postcode</label>
                <input type="text" name="postcode" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="">-- Select Type --</option>
                    <option value="Flat">Flat</option>
                    <option value="House">House</option>
                    <option value="Studio">Studio</option>
                    <option value="Detached">Detached</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Rooms</label>
                <input type="number" name="rooms" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label>Monthly Rent (£)</label>
                <input type="number" name="rent" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label>Owner</label>
                <select name="owner_no" class="form-control">
                    <option value="">-- Select Owner --</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->owner_no }}">{{ $owner->f_name }} {{ $owner->l_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Branch</label>
                <select name="branch_no" class="form-control">
                    <option value="">-- Select Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->branch_no }}">{{ $branch->branch_no }} - {{ $branch->city }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="is_available" class="form-control">
                    <option value="1">Available</option>
                    <option value="0">Withdrawn</option>
                </select>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('properties.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection