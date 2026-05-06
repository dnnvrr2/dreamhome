@extends('layouts.app')

@section('content')
<h2>Record Inspection</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('inspections.store') }}" method="POST">
@csrf
<div class="row g-2">
    <div class="col-md-4">
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
    <div class="col-md-4">
        <label>Staff (Inspector)</label>
        <select name="staff_no" class="form-control" required>
            <option value="">-- Select Staff --</option>
            @foreach($staff as $s)
                <option value="{{ $s->staff_no }}" {{ old('staff_no') == $s->staff_no ? 'selected' : '' }}>
                    {{ $s->staff_no }} - {{ $s->f_name }} {{ $s->l_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label>Inspection Date</label>
        <input type="date" name="inspection_date" class="form-control" value="{{ old('inspection_date') }}" required>
    </div>
    <div class="col-md-8 mt-2">
        <label>Comments</label>
        <textarea name="comments" class="form-control" rows="3">{{ old('comments') }}</textarea>
    </div>
</div>
<div class="mt-4">
    <button type="submit" class="btn btn-primary">Save Inspection</button>
    <a href="{{ route('inspections.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>
@endsection