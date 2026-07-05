@extends('layouts.admin')

@section('title', 'Bookings — Admin')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">BOOKINGS</div>
        <div style="color:var(--text-muted); font-size:14px; margin-top:6px;">Manage all court reservations</div>
    </div>
</div>

<!-- Filters -->
<div class="stat-card mb-4">
    <form method="GET" action="{{ route('admin.bookings') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Search Player</label>
            <input type="text" name="search" class="form-control" placeholder="Name or email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn-primary-sm w-100 justify-content-center" style="padding:13px;">
                <i class="fas fa-search"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Status Tabs -->
<div style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
    @foreach(['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'] as $key => $label)
    <a href="{{ route('admin.bookings', $key !== 'all' ? ['status' => $key] : []) }}"
       style="padding:7px 16px; border-radius:100px; font-size:12px; font-weight:700; text-decoration:none; text-transform:uppercase; letter-spacing:0.5px;
              {{ (request('status', 'all') === $key || ($key === 'all' && !request('status'))) ?
                 'background:var(--primary); color:var(--dark);' :
                 'background:rgba(255,255,255,0.06); color:var(--text-muted);' }}">
        {{ $label }} ({{ $counts[$key] }})
    </a>
    @endforeach
</div>

<!-- Table -->
<div class="stat-card">
    <div class="table-dark-custom">
        <table>
            <thead>
                <tr>
                    <th>Booking #</th>
                    <th>Player</th>
                    <th>Court</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td style="font-weight:700; color:var(--primary); font-size:13px;">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="https://ui-avatars.com/api/?name={{ $booking->user->name }}&background=ffffff&color=000000&size=30&bold=true"
                                style="width:30px; height:30px; border-radius:50%;" alt="">
                            <div>
                                <div style="font-weight:600; font-size:13px;">{{ $booking->user->name }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $booking->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px; font-weight:500;">{{ $booking->court->name }}</td>
                    <td style="font-size:13px;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                    <td style="font-size:13px; color:var(--text-muted);">{{ $booking->time_slot }}</td>
                    <td style="color:var(--primary); font-weight:700; font-size:13px;">RM {{ number_format($booking->amount, 2) }}</td>
                    <td>
                        @if($booking->payment_method)
                        <span style="font-size:12px; color:#aaa;">{{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}</span>
                        @else
                        <span style="font-size:12px; color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td><span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn-outline-sm" style="padding:5px 10px; font-size:11px;" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
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
                            <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn-danger-sm" style="padding:5px 10px; font-size:11px;" title="Delete"
                                    data-confirm="Delete booking #{{ str_pad($booking->id,6,'0',STR_PAD_LEFT) }}?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding:60px; color:var(--text-muted);">
                        <i class="fas fa-calendar-times" style="font-size:32px; display:block; margin-bottom:12px; opacity:0.3;"></i>
                        No bookings found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $bookings->links() }}
    </div>
</div>

@endsection
