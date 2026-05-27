@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-1">Client Requests</h2>
        <div class="text-muted">Review public property enquiries before creating client records.</div>
    </div>
    <a href="{{ route('public.properties') }}" class="btn btn-outline-primary" target="_blank">
        <i class="bi bi-box-arrow-up-right"></i> Public Page
    </a>
</div>

<form method="GET" action="{{ route('client-requests.index') }}" class="row g-2 mb-4">
    <div class="col-md-3">
        <select name="status" class="form-control">
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $value => $label)
                <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-dark w-100">Filter</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Request</th>
                <th>Client</th>
                <th>Contact</th>
                <th>Property</th>
                <th>Preferences</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $requestItem)
            <tr>
                <td>
                    #{{ $requestItem->id }}<br>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($requestItem->created_at)->format('M d, Y') }}</small>
                </td>
                <td>
                    <strong>{{ $requestItem->f_name }} {{ $requestItem->l_name }}</strong><br>
                    <small>{{ $requestItem->city ?: 'No city provided' }}</small>
                </td>
                <td>
                    {{ $requestItem->tel_no }}<br>
                    <small>{{ $requestItem->email ?: 'No email provided' }}</small>
                </td>
                <td>
                    <strong>{{ $requestItem->property_no ?: 'N/A' }}</strong><br>
                    <small>{{ $requestItem->property_street ?: 'Property removed' }} {{ $requestItem->property_city ? ', ' . $requestItem->property_city : '' }}</small>
                </td>
                <td>
                    {{ $requestItem->pref_type ?: $requestItem->property_type ?: 'Any type' }}<br>
                    <small>
                        Max:
                        {{ $requestItem->max_rent ? 'GBP ' . number_format($requestItem->max_rent, 2) : 'Not set' }}
                        @if($requestItem->preferred_view_date)
                            | View: {{ \Carbon\Carbon::parse($requestItem->preferred_view_date)->format('M d, Y') }}
                        @endif
                    </small>
                </td>
                <td>
                    @if($requestItem->status === 'approved')
                        <span class="badge bg-success">Approved</span>
                        <div><small>Client {{ $requestItem->approved_client_no }}</small></div>
                    @elseif($requestItem->status === 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </td>
                <td>
                    @if($requestItem->status === 'pending')
                        <form action="{{ route('client-requests.approve', $requestItem->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <form action="{{ route('client-requests.reject', $requestItem->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject this request?')">Reject</button>
                        </form>
                    @else
                        <span class="text-muted">Processed</span>
                    @endif
                </td>
            </tr>
            @if($requestItem->comments)
            <tr class="table-light">
                <td></td>
                <td colspan="6"><strong>Message:</strong> {{ $requestItem->comments }}</td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="7" class="text-center">No requests found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
