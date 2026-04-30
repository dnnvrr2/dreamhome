@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Owners</h2>
    <a href="{{ route('owners.create') }}" class="btn btn-primary">Add Owner</a>
</div>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Owner No</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Street</th>
            <th>City</th>
            <th>Tel No</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($owners as $owner)
        <tr>
            <td>{{ $owner->owner_no }}</td>
            <td>{{ $owner->f_name }}</td>
            <td>{{ $owner->l_name }}</td>
            <td>{{ $owner->street ?? 'N/A' }}</td>
            <td>{{ $owner->city ?? 'N/A' }}</td>
            <td>{{ $owner->tel_no ?? 'N/A' }}</td>
            <td>
                <a href="{{ route('owners.edit', $owner->owner_no) }}" 
                   class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('owners.destroy', $owner->owner_no) }}" 
                      method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this owner?')" 
                            class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">No owners found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection