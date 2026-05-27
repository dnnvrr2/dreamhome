@extends('layouts.app')

@section('content')
<h2 class="mb-3">Edit Property Advert</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('adverts.update', $advert->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-4">
            <label>Property</label>
            <select name="property_no" class="form-control" required>
                @foreach($properties as $property)
                    <option value="{{ $property->property_no }}" {{ old('property_no', $advert->property_no) == $property->property_no ? 'selected' : '' }}>
                        {{ $property->property_no }} - {{ $property->street }}, {{ $property->city }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label>Existing Newspaper</label>
            <select name="newspaper_id" class="form-control">
                <option value="">-- New Newspaper --</option>
                @foreach($newspapers as $newspaper)
                    <option value="{{ $newspaper->id }}" {{ old('newspaper_id', $advert->newspaper_id) == $newspaper->id ? 'selected' : '' }}>
                        {{ $newspaper->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label>Newspaper Name</label>
            <input type="text" name="newspaper_name" class="form-control" value="{{ old('newspaper_name') }}">
        </div>
        <div class="col-md-3">
            <label>Advert Date</label>
            <input type="date" name="advert_date" class="form-control" value="{{ old('advert_date', $advert->advert_date) }}" required>
        </div>
        <div class="col-md-3">
            <label>Cost</label>
            <input type="number" name="cost" class="form-control" step="0.01" min="0" value="{{ old('cost', $advert->cost) }}">
        </div>
        <div class="col-md-12">
            <label>Comments</label>
            <textarea name="comments" class="form-control" rows="3">{{ old('comments', $advert->comments) }}</textarea>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Update Advert</button>
        <a href="{{ route('adverts.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
