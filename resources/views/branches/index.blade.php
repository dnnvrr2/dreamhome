@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Branches</h2>
    <a href="{{ route('branches.create') }}" class="btn btn-primary">Add Branch</a>
</div>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Branch No</th>
            <th>Street</th>
            <th>Area</th>
            <th>City</th>
            <th>Postcode</th>
            <th>Tel No</th>
            <th>Fax No</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($branches as $branch)
        <tr>
            <td>{{ $branch->branch_no }}</td>
            <td>{{ $branch->street }}</td>
            <td>{{ $branch->area ?? 'N/A' }}</td>
            <td>{{ $branch->city }}</td>
            <td>{{ $branch->postcode }}</td>
            <td>{{ $branch->tel_no ?? 'N/A' }}</td>
            <td>{{ $branch->fax_no ?? 'N/A' }}</td>
            <td>
                <a href="{{ route('branches.edit', $branch->branch_no) }}" 
                   class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('branches.destroy', $branch->branch_no) }}" 
                      method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this branch?')" 
                            class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center">No branches found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection