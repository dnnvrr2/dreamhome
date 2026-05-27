@extends('layouts.app')

@section('content')
<h2>Edit Property</h2>
<form action="{{ route('properties.update', $property->property_no) }}" method="POST">
    @csrf @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label>Property No</label>
                <input type="text" class="form-control" value="{{ $property->property_no }}" disabled>
            </div>
            <div class="mb-3">
                <label>Street</label>
                <input type="text" name="street" class="form-control" value="{{ $property->street }}" required>
            </div>
            <div class="mb-3">
                <label>Area</label>
                <input type="text" name="area" class="form-control" value="{{ $property->area }}">
            </div>
            <div class="mb-3">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="{{ $property->city }}" required>
            </div>
            <div class="mb-3">
                <label>Postcode</label>
                <input type="text" name="postcode" class="form-control" value="{{ $property->postcode }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="">-- Select Type --</option>
                    @foreach(['Flat','House','Studio','Detached'] as $type)
                        <option value="{{ $type }}" {{ $property->type == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Rooms</label>
                <input type="number" name="rooms" class="form-control" value="{{ $property->rooms }}" min="1" required>
            </div>
            <div class="mb-3">
                <label>Monthly Rent (£)</label>
                <input type="number" name="rent" class="form-control" value="{{ $property->rent }}" step="0.01" required>
            </div>
            <div class="mb-3">
                <label>Owner</label>
                <select name="owner_no" class="form-control">
                    <option value="">-- Select Owner --</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->owner_no }}" {{ $property->owner_no == $owner->owner_no ? 'selected' : '' }}>
                            {{ $owner->f_name }} {{ $owner->l_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Branch</label>
                <select name="branch_no" class="form-control">
                    <option value="">-- Select Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->branch_no }}" {{ $property->branch_no == $branch->branch_no ? 'selected' : '' }}>
                            {{ $branch->branch_no }} - {{ $branch->city }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Managed By</label>
                <select name="staff_no" class="form-control">
                    <option value="">-- Select Staff --</option>
                    @foreach($staff as $s)
                        <option value="{{ $s->staff_no }}" {{ $property->staff_no == $s->staff_no ? 'selected' : '' }}>
                            {{ $s->staff_no }} - {{ $s->f_name }} {{ $s->l_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="is_available" class="form-control">
                    <option value="1" {{ $property->is_available ? 'selected' : '' }}>Available</option>
                    <option value="0" {{ !$property->is_available ? 'selected' : '' }}>Withdrawn</option>
                </select>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('properties.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
