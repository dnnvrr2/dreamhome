@extends('layouts.app')

@section('content')
<h2>Edit Owner</h2>
<form action="{{ route('owners.update', $owner->owner_no) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Owner No</label>
        <input type="text" class="form-control" value="{{ $owner->owner_no }}" disabled>
    </div>
    <div class="mb-3">
        <label>First Name</label>
        <input type="text" name="f_name" class="form-control" value="{{ $owner->f_name }}" required>
    </div>
    <div class="mb-3">
        <label>Last Name</label>
        <input type="text" name="l_name" class="form-control" value="{{ $owner->l_name }}" required>
    </div>
    <div class="mb-3">
        <label>Street</label>
        <input type="text" name="street" class="form-control" value="{{ $owner->street }}">
    </div>
    <div class="mb-3">
        <label>City</label>
        <input type="text" name="city" class="form-control" value="{{ $owner->city }}">
    </div>
    <div class="mb-3">
        <label>Postcode</label>
        <input type="text" name="postcode" class="form-control" value="{{ $owner->postcode }}">
    </div>
    <div class="mb-3">
        <label>Tel No</label>
        <input type="text" name="tel_no" class="form-control" value="{{ $owner->tel_no }}">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('owners.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection