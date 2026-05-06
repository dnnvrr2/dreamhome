@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Staff</h2>
    <a href="{{ route('staff.create') }}" class="btn btn-primary">Add Staff</a>
</div>

<form method="GET" action="{{ route('staff.index') }}" class="row g-2 mb-4">
    <div class="col-md-3">
        <select name="branch_no" class="form-control">
            <option value="">-- All Branches --</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->branch_no }}"
                    {{ request('branch_no') == $branch->branch_no ? 'selected' : '' }}>
                    {{ $branch->branch_no }} - {{ $branch->city }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="position" class="form-control">
            <option value="">-- All Positions --</option>
            @foreach(['Manager','Supervisor','Secretary','Staff'] as $pos)
                <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>
                    {{ $pos }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-dark w-100">Filter</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('staff.index') }}" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Staff No</th>
            <th>Name</th>
            <th>Position</th>
            <th>Branch</th>
            <th>Salary</th>
            <th>Supervisor</th>
            <th>Date Joined</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($staff as $s)
        <tr>
            <td>{{ $s->staff_no }}</td>
            <td>{{ $s->f_name }} {{ $s->l_name }}</td>
            <td><span class="badge bg-secondary">{{ $s->position }}</span></td>
            <td>{{ $s->branch_city ?? 'N/A' }}</td>
            <td>£{{ number_format($s->salary, 2) }}</td>
            <td>{{ $s->sup_fname ?? 'None' }} {{ $s->sup_lname ?? '' }}</td>
            <td>{{ $s->date_joined ?? 'N/A' }}</td>
            <td>
                <a href="{{ route('staff.show', $s->staff_no) }}" class="btn btn-sm btn-info">View</a>
                <a href="{{ route('staff.edit', $s->staff_no) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('staff.destroy', $s->staff_no) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this staff member?')"
                            class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center">No staff found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection