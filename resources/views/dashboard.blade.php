@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Dashboard</h2>
        <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}</p>
    </div>
</div>

{{-- Stats Grid --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-dark text-white">
            <div class="card-body">
                <div class="small text-white-50 mb-1">Total Properties</div>
                <div class="fs-2 fw-bold">{{ $totalProperties }}</div>
                <div class="small text-success mt-1">{{ $availableProperties }} available</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="background:#0f3460; color:white;">
            <div class="card-body">
                <div class="small mb-1" style="color:rgba(255,255,255,0.6);">Total Staff</div>
                <div class="fs-2 fw-bold">{{ $totalStaff }}</div>
                <div class="small mt-1" style="color:rgba(255,255,255,0.6);">across all branches</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="background:#e94560; color:white;">
            <div class="card-body">
                <div class="small mb-1" style="color:rgba(255,255,255,0.7);">Active Leases</div>
                <div class="fs-2 fw-bold">{{ $totalLeases }}</div>
                <div class="small mt-1" style="color:rgba(255,255,255,0.7);">lease agreements</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success text-white">
            <div class="card-body">
                <div class="small text-white-50 mb-1">Clients</div>
                <div class="fs-2 fw-bold">{{ $totalClients }}</div>
                <div class="small text-white-50 mt-1">registered renters</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 bg-secondary text-white">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold">{{ $totalBranches }}</div>
                <div class="small">Branch Offices</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-warning">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold">{{ $totalOwners }}</div>
                <div class="small">Property Owners</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-info text-white">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold">{{ $totalInspections }}</div>
                <div class="small">Inspections Done</div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Activity --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark text-white">
                Recent Properties
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Property</th>
                            <th>Owner</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProperties as $p)
                        <tr>
                            <td>{{ $p->street }}, {{ $p->city }}</td>
                            <td>{{ $p->owner_fname }} {{ $p->owner_lname }}</td>
                            <td>
                                @if($p->is_available)
                                    <span class="badge bg-success">Available</span>
                                @else
                                    <span class="badge bg-danger">Withdrawn</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center">No properties</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark text-white">
                Recent Inspections
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Property</th>
                            <th>Staff</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInspections as $i)
                        <tr>
                            <td>{{ $i->street }}, {{ $i->city }}</td>
                            <td>{{ $i->staff_fname }} {{ $i->staff_lname }}</td>
                            <td>{{ $i->inspection_date }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center">No inspections</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection