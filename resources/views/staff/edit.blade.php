@extends('layouts.app')

@section('content')
<h2>Edit Staff — {{ $staff->staff_no }}</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('staff.update', $staff->staff_no) }}" method="POST">
@csrf @method('PUT')

<h5 class="mt-3">Basic Information</h5>
<div class="row g-2">
    <div class="col-md-2">
        <label>Staff No</label>
        <input type="text" class="form-control" value="{{ $staff->staff_no }}" disabled>
    </div>
    <div class="col-md-3">
        <label>First Name</label>
        <input type="text" name="f_name" class="form-control" value="{{ old('f_name', $staff->f_name) }}" required>
    </div>
    <div class="col-md-3">
        <label>Last Name</label>
        <input type="text" name="l_name" class="form-control" value="{{ old('l_name', $staff->l_name) }}" required>
    </div>
    <div class="col-md-2">
        <label>Sex</label>
        <select name="sex" class="form-control">
            <option value="">--</option>
            <option value="M" {{ $staff->sex == 'M' ? 'selected' : '' }}>Male</option>
            <option value="F" {{ $staff->sex == 'F' ? 'selected' : '' }}>Female</option>
        </select>
    </div>
    <div class="col-md-2">
        <label>Date of Birth</label>
        <input type="date" name="dob" class="form-control" value="{{ old('dob', $staff->dob) }}">
    </div>
</div>

<div class="row g-2 mt-1">
    <div class="col-md-3">
        <label>NIN</label>
        <input type="text" name="nin" class="form-control" value="{{ old('nin', $staff->nin) }}">
    </div>
    <div class="col-md-3">
        <label>Position</label>
        <select name="position" class="form-control" id="positionSelect" required>
            @foreach(['Manager','Supervisor','Secretary','Staff'] as $pos)
                <option value="{{ $pos }}" {{ old('position', $staff->position) == $pos ? 'selected' : '' }}>{{ $pos }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label>Salary</label>
        <input type="number" name="salary" step="0.01" class="form-control" value="{{ old('salary', $staff->salary) }}">
    </div>
    <div class="col-md-3">
        <label>Date Joined</label>
        <input type="date" name="date_joined" class="form-control" value="{{ old('date_joined', $staff->date_joined) }}">
    </div>
</div>

<h5 class="mt-3">Address & Contact</h5>
<div class="row g-2">
    <div class="col-md-4">
        <label>Street</label>
        <input type="text" name="street" class="form-control" value="{{ old('street', $staff->street) }}">
    </div>
    <div class="col-md-3">
        <label>Area</label>
        <input type="text" name="area" class="form-control" value="{{ old('area', $staff->area) }}">
    </div>
    <div class="col-md-3">
        <label>City</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $staff->city) }}">
    </div>
    <div class="col-md-2">
        <label>Postcode</label>
        <input type="text" name="postcode" class="form-control" value="{{ old('postcode', $staff->postcode) }}">
    </div>
    <div class="col-md-3 mt-2">
        <label>Tel No</label>
        <input type="text" name="tel_no" class="form-control" value="{{ old('tel_no', $staff->tel_no) }}">
    </div>
    <div class="col-md-3 mt-2">
        <label>Branch</label>
        <select name="branch_no" class="form-control">
            <option value="">-- Select Branch --</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->branch_no }}" {{ old('branch_no', $staff->branch_no) == $branch->branch_no ? 'selected' : '' }}>
                    {{ $branch->branch_no }} - {{ $branch->city }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mt-2">
        <label>Supervisor</label>
        <select name="supervisor_no" class="form-control">
            <option value="">-- None --</option>
            @foreach($supervisors as $sup)
                <option value="{{ $sup->staff_no }}" {{ old('supervisor_no', $staff->supervisor_no) == $sup->staff_no ? 'selected' : '' }}>
                    {{ $sup->staff_no }} - {{ $sup->f_name }} {{ $sup->l_name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- Manager Fields --}}
<div id="managerFields" class="mt-3" style="display:none;">
    <h5>Manager Details</h5>
    <div class="row g-2">
        <div class="col-md-3">
            <label>Date Started as Manager</label>
            <input type="date" name="date_start" class="form-control"
                   value="{{ old('date_start', $manager->date_start ?? '') }}">
        </div>
        <div class="col-md-3">
            <label>Car Allowance (£)</label>
            <input type="number" name="car_allowance" step="0.01" class="form-control"
                   value="{{ old('car_allowance', $manager->car_allowance ?? '') }}">
        </div>
        <div class="col-md-3">
            <label>Monthly Bonus (£)</label>
            <input type="number" name="bonus" step="0.01" class="form-control"
                   value="{{ old('bonus', $manager->bonus ?? '') }}">
        </div>
    </div>
</div>

{{-- Secretary Fields --}}
<div id="secretaryFields" class="mt-3" style="display:none;">
    <h5>Secretary Details</h5>
    <div class="row g-2">
        <div class="col-md-3">
            <label>Typing Speed (WPM)</label>
            <input type="number" name="typing_speed" class="form-control"
                   value="{{ old('typing_speed', $secretary->typing_speed ?? '') }}">
        </div>
    </div>
</div>

<h5 class="mt-3">Next of Kin</h5>
<div class="row g-2">
    <div class="col-md-4">
        <label>Full Name</label>
        <input type="text" name="kin_full_name" class="form-control" value="{{ old('kin_full_name', $kin->full_name ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>Relationship</label>
        <input type="text" name="kin_relationship" class="form-control" value="{{ old('kin_relationship', $kin->relationship ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>Street</label>
        <input type="text" name="kin_street" class="form-control" value="{{ old('kin_street', $kin->street ?? '') }}">
    </div>
    <div class="col-md-3 mt-2">
        <label>City</label>
        <input type="text" name="kin_city" class="form-control" value="{{ old('kin_city', $kin->city ?? '') }}">
    </div>
    <div class="col-md-3 mt-2">
        <label>Tel No</label>
        <input type="text" name="kin_tel_no" class="form-control" value="{{ old('kin_tel_no', $kin->tel_no ?? '') }}">
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">Update Staff</button>
    <a href="{{ route('staff.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>

<script>
const posSelect = document.getElementById('positionSelect');
function toggleFields() {
    const val = posSelect.value;
    document.getElementById('managerFields').style.display  = val === 'Manager'   ? 'block' : 'none';
    document.getElementById('secretaryFields').style.display = val === 'Secretary' ? 'block' : 'none';
}
posSelect.addEventListener('change', toggleFields);
toggleFields();
</script>
@endsection