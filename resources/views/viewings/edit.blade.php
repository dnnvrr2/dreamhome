@extends('layouts.app')

@section('content')
<h2>Edit Viewing</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('viewings.update', $viewing->client_no) }}" method="POST">
@csrf @method('PUT')
<input type="hidden" name="property_no" value="{{ $viewing->property_no }}">
<input type="hidden" name="view_date" value="{{ $viewing->view_date }}">

<div class="row g-2">
    <div class="col-md-3">
        <label>Client</label>
        <input type="text" class="form-control" value="{{ $viewing->client_no }}" disabled>
    </div>
    <div class="col-md-3">
        <label>Property</label>
        <input type="text" class="form-control" value="{{ $viewing->property_no }}" disabled>
    </div>
    <div class="col-md-3">
        <label>View Date</label>
        <input type="text" class="form-control" value="{{ $viewing->view_date }}" disabled>
    </div>
    <div class="col-md-3">
        <label>Staff</label>
        <select name="staff_no" class="form-control">
            <option value="">-- Select Staff --</option>
            @foreach($staff as $s)
                <option value="{{ $s->staff_no }}" {{ $viewing->staff_no == $s->staff_no ? 'selected' : '' }}>
                    {{ $s->staff_no }} - {{ $s->f_name }} {{ $s->l_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mt-2">
        <label>Comments</label>
        <textarea name="comments" class="form-control" rows="3">{{ $viewing->comments }}</textarea>
    </div>
</div>
<div class="mt-4">
    <button type="submit" class="btn btn-primary">Update Viewing</button>
    <a href="{{ route('viewings.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>
@endsection