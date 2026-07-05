@extends('layouts.app')

@section('title', 'My Bookings — Daiman Sports Complex')

@section('content')

<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-title">MY BOOKINGS</div>
                <div class="page-subtitle">Manage your court reservations and access QR codes</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('booking.index') }}" class="btn-primary-sm" style="padding:12px 24px;">
                    <i class="fas fa-plus"></i> New Booking
                </a>
            </div>
        </div>
    </div>
</div>

<div class="section section-dark">
    <div class="container">

        <!-- Filter Tabs -->
        <div style="display:flex; gap:8px; margin-bottom:32px; flex-wrap:wrap;">
            <a href="{{ route('booking.history') }}" class="status-badge {{ !request('status') ? 'status-confirmed' : '' }}" style="text-decoration:none; padding:8px 18px; font-size:13px; {{ !request('status') ? '' : 'background:rgba(255,255,255,0.05); color:var(--text-muted);' }}">
                All <span style="opacity:0.7;">({{ $totalCount }})</span>
            </a>
            <a href="{{ route('booking.history', ['status' => 'confirmed']) }}" class="status-badge {{ request('status') == 'confirmed' ? 'status-confirmed' : '' }}" style="text-decoration:none; padding:8px 18px; font-size:13px; {{ request('status') == 'confirmed' ? '' : 'background:rgba(255,255,255,0.05); color:var(--text-muted);' }}">
                Confirmed
            </a>
            <a href="{{ route('booking.history', ['status' => 'pending']) }}" class="status-badge {{ request('status') == 'pending' ? 'status-pending' : '' }}" style="text-decoration:none; padding:8px 18px; font-size:13px; {{ request('status') == 'pending' ? '' : 'background:rgba(255,255,255,0.05); color:var(--text-muted);' }}">
                Pending
            </a>
            <a href="{{ route('booking.history', ['status' => 'cancelled']) }}" class="status-badge {{ request('status') == 'cancelled' ? 'status-cancelled' : '' }}" style="text-decoration:none; padding:8px 18px; font-size:13px; {{ request('status') == 'cancelled' ? '' : 'background:rgba(255,255,255,0.05); color:var(--text-muted);' }}">
                Cancelled
            </a>
        </div>

        @if($bookings->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-calendar-times"></i></div>
                <div class="empty-title">No bookings found</div>
                <div class="empty-desc" style="margin-bottom:24px;">You haven't made any court reservations yet.</div>
                <a href="{{ route('booking.index') }}" class="btn-primary-sm" style="padding:14px 28px; font-size:15px;">
                    <i class="fas fa-calendar-plus"></i> Book Your First Court
                </a>
            </div>
        @else
            @foreach($bookings as $booking)
            <div class="booking-item">
                <div class="booking-court-icon">
                    🏸
                </div>
                <div class="booking-info">
                    <div class="booking-court-name">{{ $booking->court->name }}</div>
                    <div class="booking-meta">
                        <span><i class="fas fa-calendar"></i>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                        <span><i class="fas fa-clock"></i>{{ $booking->time_slot }}</span>
                        <span><i class="fas fa-tag"></i>RM {{ number_format($booking->amount, 2) }}</span>
                        <span><i class="fas fa-hashtag"></i>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    @if($booking->notes)
                    <div style="font-size:12px; color:var(--text-muted); margin-top:4px;"><i class="fas fa-sticky-note me-1"></i>{{ $booking->notes }}</div>
                    @endif
                </div>
                <div class="d-flex flex-column align-items-end gap-2">
                    <span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                    <div class="d-flex gap-2">
                        @if($booking->status === 'confirmed')
                        <a href="{{ route('booking.show', $booking->id) }}" class="btn-primary-sm" style="padding:7px 14px; font-size:12px;">
                            <i class="fas fa-qrcode"></i> QR Code
                        </a>
                        @endif
                        @if(in_array($booking->status, ['confirmed', 'pending']) && \Carbon\Carbon::parse($booking->booking_date)->isFuture())
                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-danger-sm" style="padding:7px 14px; font-size:12px;"
                                data-confirm="Are you sure you want to cancel this booking?">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
