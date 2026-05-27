@extends('layouts.app')

@section('content')
<h2>Add Property</h2>
<form action="{{ route('properties.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label>Property No</label>
                <input type="text" id="property_no_preview" class="form-control" value="" readonly>
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
                        <option value="{{ $branch->branch_no }}" {{ old('branch_no') == $branch->branch_no ? 'selected' : '' }}>
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
                        <option value="{{ $s->staff_no }}" {{ old('staff_no') == $s->staff_no ? 'selected' : '' }}>
                            {{ $s->staff_no }} - {{ $s->f_name }} {{ $s->l_name }}
                        </option>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const branchSelect = document.querySelector('[name="branch_no"]');
    const propertyNoPreview = document.getElementById('property_no_preview');
    const nextPropertyNos = @json($branchPropertyNos);

    const updatePropertyNo = () => {
        propertyNoPreview.value = nextPropertyNos[branchSelect.value] || '';
    };

    branchSelect.addEventListener('change', updatePropertyNo);
    updatePropertyNo();
});
</script>
@endsection
