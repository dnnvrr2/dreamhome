
@extends('layouts.app')

@section('content')
<h2>Add Owner</h2>
<form action="{{ route('owners.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Owner No</label>
        <input type="text" name="owner_no" class="form-control" maxlength="5" required>
    </div>
    <div class="mb-3">
        <label>First Name</label>
        <input type="text" name="f_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Last Name</label>
        <input type="text" name="l_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Street</label>
        <input type="text" name="street" class="form-control">
    </div>
    <div class="mb-3">
        <label>City</label>
        <input type="text" name="city" class="form-control">
    </div>
    <div class="mb-3">
        <label>Postcode</label>
        <input type="text" name="postcode" class="form-control">
    </div>
    <div class="mb-3">
        <label>Tel No</label>
        <input type="text" name="tel_no" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('owners.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection