@extends('layouts.admin')

@section('title', 'Booking Detail — Admin')

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.bookings') }}" style="color:var(--text-muted); text-decoration:none; font-size:14px; display:inline-flex; align-items:center; gap:8px; margin-bottom:16px;">
        <i class="fas fa-arrow-left"></i> Back to Bookings
    </a>
    <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">
            BOOKING #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
        </div>
        <span class="status-badge status-{{ $booking->status }}" style="font-size:14px; padding:8px 16px;">{{ ucfirst($booking->status) }}</span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="stat-card mb-4">
            <div style="font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--primary); margin-bottom:20px;">
                <i class="fas fa-calendar-check me-2"></i>Booking Details
            </div>
            @foreach([
                ['Court',    $booking->court->name],
                ['Type',     $booking->court->type],
                ['Date',     \Carbon\Carbon::parse($booking->booking_date)->format('l, d F Y')],
                ['Time',     $booking->time_slot],
                ['Amount',   'RM ' . number_format($booking->amount, 2)],
                ['Payment',  ucfirst(str_replace('_', ' ', $booking->payment_method ?? '—'))],
                ['Pay Status',ucfirst($booking->payment_status)],
                ['QR Token', $booking->qr_token],
                ['Booked On',$booking->created_at->format('d M Y H:i')],
                ['QR Scanned',$booking->qr_scanned_at ? $booking->qr_scanned_at->format('d M Y H:i') : 'Not yet scanned'],
            ] as [$label, $value])
            <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--border); font-size:14px;">
                <span style="color:var(--text-muted);">{{ $label }}</span>
                <span style="font-weight:600;">{{ $value }}</span>
            </div>
            @endforeach
            @if($booking->notes)
            <div style="padding:12px 0; font-size:14px;">
                <span style="color:var(--text-muted);">Notes</span><br>
                <span style="color:var(--light); margin-top:4px; display:block;">{{ $booking->notes }}</span>
            </div>
            @endif
        </div>

        <!-- Player Info -->
        <div class="stat-card">
            <div style="font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--primary); margin-bottom:20px;">
                <i class="fas fa-user me-2"></i>Player Information
            </div>
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                <img src="https://ui-avatars.com/api/?name={{ $booking->user->name }}&background=ffffff&color=000000&size=64&bold=true"
                    style="width:64px; height:64px; border-radius:50%; border:3px solid rgba(0,230,118,0.2);" alt="">
                <div>
                    <div style="font-size:18px; font-weight:700;">{{ $booking->user->name }}</div>
                    <div style="color:var(--text-muted); font-size:13px;">{{ $booking->user->email }}</div>
                    @if($booking->user->phone)
                    <div style="color:var(--text-muted); font-size:13px;">{{ $booking->user->phone }}</div>
                    @endif
                </div>
            </div>
            <div style="font-size:13px; color:var(--text-muted); padding:10px 0; border-top:1px solid var(--border);">
                Total bookings by this user:
                <strong style="color:var(--light);">{{ $booking->user->bookings()->count() }}</strong>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <!-- QR Code -->
        <div class="stat-card text-center mb-4">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:var(--primary); margin-bottom:20px;">
                <i class="fas fa-qrcode me-2"></i>Entry QR Code
            </div>
            <div class="qr-container">
                {!! $qrCode !!}
            </div>
            <div style="margin-top:14px; font-size:13px; color:var(--text-muted);">
                Token: <strong style="color:var(--primary); letter-spacing:2px; font-family:monospace;">{{ $booking->qr_token }}</strong>
            </div>
            @if($booking->qr_scanned_at)
            <div style="margin-top:10px; padding:8px 16px; background:rgba(0,214,143,0.1); border-radius:100px; display:inline-block;">
                <span style="color:var(--success); font-size:12px; font-weight:700;">
                    <i class="fas fa-check-circle me-1"></i>Scanned at {{ $booking->qr_scanned_at->format('H:i d M Y') }}
                </span>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="stat-card">
            <div style="font-size:14px; font-weight:700; margin-bottom:16px;">Actions</div>
            <div class="d-flex flex-column gap-2">
                @if($booking->status === 'pending')
                <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="btn-primary-sm w-100 justify-content-center" style="padding:12px;">
                        <i class="fas fa-check"></i> Approve Booking
                    </button>
                </form>
                <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="btn-danger-sm w-100 justify-content-center" style="padding:12px;">
                        <i class="fas fa-times"></i> Reject Booking
                    </button>
                </form>
                @endif
                <a href="{{ route('admin.qr-scanner') }}" class="btn-outline-sm w-100 justify-content-center" style="padding:12px;">
                    <i class="fas fa-qrcode"></i> Go to QR Scanner
                </a>
                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn-danger-sm w-100 justify-content-center" style="padding:12px;"
                        data-confirm="Delete this booking permanently?">
                        <i class="fas fa-trash"></i> Delete Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
