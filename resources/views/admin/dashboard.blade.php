@extends('layouts.admin')

@section('title', 'Dashboard — Admin')

@section('content')

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-5">
    <div>
        <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">DASHBOARD</div>
        <div style="color:var(--text-muted); font-size:14px; margin-top:6px;">
            Welcome back, <strong style="color:var(--light);">{{ auth()->user()->name }}</strong> — {{ now()->format('l, d M Y') }}
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.bookings') }}" class="btn-primary-sm" style="padding:12px 20px;">
            <i class="fas fa-eye"></i> View Bookings
        </a>
        <a href="{{ route('admin.reports') }}" class="btn-outline-sm" style="padding:12px 20px;">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon icon-yellow"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-card-value">{{ $stats['total_bookings'] }}</div>
            <div class="stat-card-label">Total Bookings</div>
            <div class="stat-card-change change-up"><i class="fas fa-arrow-up me-1"></i>{{ $stats['bookings_today'] }} today</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-card-value">RM {{ number_format($stats['total_revenue'], 0) }}</div>
            <div class="stat-card-label">Total Revenue</div>
            <div class="stat-card-change change-up"><i class="fas fa-arrow-up me-1"></i>RM {{ number_format($stats['revenue_today'], 2) }} today</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon icon-blue"><i class="fas fa-users"></i></div>
            <div class="stat-card-value">{{ $stats['total_users'] }}</div>
            <div class="stat-card-label">Registered Users</div>
            <div class="stat-card-change change-up"><i class="fas fa-arrow-up me-1"></i>{{ $stats['new_users_week'] }} this week</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon icon-red"><i class="fas fa-clock"></i></div>
            <div class="stat-card-value">{{ $stats['pending_bookings'] }}</div>
            <div class="stat-card-label">Pending Bookings</div>
            <div class="stat-card-change {{ $stats['pending_bookings'] > 0 ? 'change-down' : 'change-up' }}">
                {{ $stats['pending_bookings'] > 0 ? 'Needs attention' : 'All clear!' }}
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Bookings -->
    <div class="col-lg-8">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div style="font-size:16px; font-weight:700;">Recent Bookings</div>
                <a href="{{ route('admin.bookings') }}" class="btn-outline-sm" style="font-size:12px; padding:6px 14px;">View All</a>
            </div>
            <div class="table-dark-custom">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Player</th>
                            <th>Court</th>
                            <th>Date & Time</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                        <tr>
                            <td style="color:var(--text-muted);">{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div style="font-weight:600; font-size:13px;">{{ $booking->user->name }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $booking->user->email }}</div>
                            </td>
                            <td>{{ $booking->court->name }}</td>
                            <td>
                                <div style="font-size:13px;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $booking->time_slot }}</div>
                            </td>
                            <td style="color:var(--primary); font-weight:700;">RM {{ number_format($booking->amount, 2) }}</td>
                            <td><span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($booking->status === 'pending')
                                    <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button class="btn-primary-sm" style="padding:5px 10px; font-size:11px;" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button class="btn-danger-sm" style="padding:5px 10px; font-size:11px;" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn-outline-sm" style="padding:5px 10px; font-size:11px;" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);">No bookings yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Court Status -->
        <div class="stat-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div style="font-size:16px; font-weight:700;">Court Status</div>
                <a href="{{ route('admin.courts') }}" class="btn-outline-sm" style="font-size:12px; padding:6px 14px;">Manage</a>
            </div>
            @foreach($courts as $court)
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:8px; height:8px; border-radius:50%; background:{{ $court->is_available ? 'var(--success)' : 'var(--danger)' }}; box-shadow:0 0 0 3px {{ $court->is_available ? 'rgba(0,214,143,0.2)' : 'rgba(255,61,87,0.2)' }};"></div>
                    <div>
                        <div style="font-size:14px; font-weight:600;">{{ $court->name }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $court->type }}</div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:14px; color:var(--primary); font-weight:700;">RM {{ number_format($court->price_per_hour, 2) }}/hr</div>
                    <div style="font-size:11px; color:var(--text-muted);">{{ $court->is_available ? 'Available' : 'Blocked' }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Quick Actions -->
        <div class="stat-card">
            <div style="font-size:16px; font-weight:700; margin-bottom:20px;">Quick Actions</div>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('admin.courts.create') }}" class="btn-primary-sm justify-content-center" style="padding:12px; font-size:13px;">
                    <i class="fas fa-plus"></i> Add New Court
                </a>
                <a href="{{ route('admin.qr-scanner') }}" class="btn-outline-sm justify-content-center" style="padding:12px; font-size:13px;">
                    <i class="fas fa-qrcode"></i> QR Code Scanner
                </a>
                <a href="{{ route('admin.reports') }}" class="btn-outline-sm justify-content-center" style="padding:12px; font-size:13px;">
                    <i class="fas fa-download"></i> Export Reports
                </a>
                <a href="{{ route('admin.users') }}" class="btn-outline-sm justify-content-center" style="padding:12px; font-size:13px;">
                    <i class="fas fa-users"></i> Manage Users
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
