@extends('layouts.app')

@section('content')
<h2>Edit Branch</h2>
<form action="{{ route('branches.update', $branch->branch_no) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Branch No</label>
        <input type="text" class="form-control" value="{{ $branch->branch_no }}" disabled>
    </div>
    <div class="mb-3">
        <label>Street</label>
        <input type="text" name="street" class="form-control" value="{{ $branch->street }}" required>
    </div>
    <div class="mb-3">
        <label>Area</label>
        <input type="text" name="area" class="form-control" value="{{ $branch->area }}">
    </div>
    <div class="mb-3">
        <label>City</label>
        <input type="text" name="city" class="form-control" value="{{ $branch->city }}" required>
    </div>
    <div class="mb-3">
        <label>Postcode</label>
        <input type="text" name="postcode" class="form-control" value="{{ $branch->postcode }}" required>
    </div>
    <div class="mb-3">
        <label>Tel No</label>
        <input type="text" name="tel_no" class="form-control" value="{{ $branch->tel_no }}">
    </div>
    <div class="mb-3">
        <label>Fax No</label>
        <input type="text" name="fax_no" class="form-control" value="{{ $branch->fax_no }}">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection