@extends('layouts.app')

@section('content')
<h2>Add Branch</h2>
<form action="{{ route('branches.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Branch No</label>
        <input type="text" name="branch_no" class="form-control" maxlength="4" required>
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
        <input type="text" name="postcode" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Tel No</label>
        <input type="text" name="tel_no" class="form-control">
    </div>
    <div class="mb-3">
        <label>Fax No</label>
        <input type="text" name="fax_no" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection