@extends('layouts.app')

@section('title', 'Booking Confirmed — Daiman Sports Complex')

@section('content')

<div class="section section-dark">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Confirmed Header -->
                <div class="booking-confirmed-card mb-4">
                    <div class="confirmed-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1 style="font-family:var(--font-display); font-size:48px; letter-spacing:1px; margin-bottom:10px;">
                        BOOKING CONFIRMED!
                    </h1>
                    <p style="color:var(--text-muted); font-size:16px;">
                        Your court has been reserved. Show the QR code below at the court entrance.
                    </p>
                    <div style="margin-top:16px; padding:10px 20px; background:rgba(0,214,143,0.1); border:1px solid rgba(0,214,143,0.2); border-radius:100px; display:inline-block;">
                        <span style="color:var(--success); font-weight:700; font-size:13px;">
                            <i class="fas fa-circle me-2" style="font-size:8px;"></i>
                            Booking #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }} — Confirmed
                        </span>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- QR Code -->
                    <div class="col-md-5">
                        <div class="booking-step text-center">
                            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:var(--text-muted); margin-bottom:20px;">
                                <i class="fas fa-qrcode me-2" style="color:var(--primary);"></i>Your Entry QR Code
                            </div>
                            <div class="qr-container">
                                {!! $qrCode !!}
                            </div>
                            <div style="margin-top:16px; font-size:13px; color:var(--text-muted);">
                                Booking Ref: <strong style="color:var(--light);">{{ $booking->qr_token }}</strong>
                            </div>
                            <div class="mt-3 d-flex gap-2 justify-content-center">
                                <button id="print-qr" class="btn-outline-sm">
                                    <i class="fas fa-print"></i> Print
                                </button>
                                <a href="{{ route('booking.qr', $booking->id) }}" download class="btn-primary-sm">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="col-md-7">
                        <div class="booking-step">
                            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:var(--text-muted); margin-bottom:20px;">
                                <i class="fas fa-info-circle me-2" style="color:var(--primary);"></i>Booking Details
                            </div>

                            <div class="d-flex flex-column gap-3">
                                <div class="summary-row" style="padding:12px 0; border-bottom:1px solid var(--border);">
                                    <span style="color:var(--text-muted); font-size:13px; display:flex; align-items:center; gap:8px;">
                                        <i class="fas fa-table-tennis" style="color:var(--primary); width:16px;"></i>Court
                                    </span>
                                    <span style="font-weight:700;">{{ $booking->court->name }}</span>
                                </div>
                                <div class="summary-row" style="padding:12px 0; border-bottom:1px solid var(--border);">
                                    <span style="color:var(--text-muted); font-size:13px; display:flex; align-items:center; gap:8px;">
                                        <i class="fas fa-map-marker-alt" style="color:var(--primary); width:16px;"></i>Type
                                    </span>
                                    <span style="font-weight:700;">{{ $booking->court->type }}</span>
                                </div>
                                <div class="summary-row" style="padding:12px 0; border-bottom:1px solid var(--border);">
                                    <span style="color:var(--text-muted); font-size:13px; display:flex; align-items:center; gap:8px;">
                                        <i class="fas fa-calendar" style="color:var(--primary); width:16px;"></i>Date
                                    </span>
                                    <span style="font-weight:700;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('D, d M Y') }}</span>
                                </div>
                                <div class="summary-row" style="padding:12px 0; border-bottom:1px solid var(--border);">
                                    <span style="color:var(--text-muted); font-size:13px; display:flex; align-items:center; gap:8px;">
                                        <i class="fas fa-clock" style="color:var(--primary); width:16px;"></i>Time
                                    </span>
                                    <span style="font-weight:700;">{{ $booking->time_slot }}</span>
                                </div>
                                <div class="summary-row" style="padding:12px 0; border-bottom:1px solid var(--border);">
                                    <span style="color:var(--text-muted); font-size:13px; display:flex; align-items:center; gap:8px;">
                                        <i class="fas fa-user" style="color:var(--primary); width:16px;"></i>Player
                                    </span>
                                    <span style="font-weight:700;">{{ $booking->user->name }}</span>
                                </div>
                                <div class="summary-row" style="padding:12px 0; border-bottom:1px solid var(--border);">
                                    <span style="color:var(--text-muted); font-size:13px; display:flex; align-items:center; gap:8px;">
                                        <i class="fas fa-credit-card" style="color:var(--primary); width:16px;"></i>Payment
                                    </span>
                                    <span class="status-badge status-confirmed">{{ ucfirst(str_replace('_', ' ', $booking->payment_method ?? 'paid')) }}</span>
                                </div>
                                <div class="summary-row" style="padding:12px 0;">
                                    <span style="color:var(--text-muted); font-size:13px; display:flex; align-items:center; gap:8px;">
                                        <i class="fas fa-receipt" style="color:var(--primary); width:16px;"></i>Amount Paid
                                    </span>
                                    <span style="font-family:var(--font-display); font-size:22px; color:var(--primary);">RM {{ number_format($booking->amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="booking-step mt-4">
                    <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:var(--text-muted); margin-bottom:20px;">
                        <i class="fas fa-info-circle me-2" style="color:var(--primary);"></i>How to Enter the Court
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div style="display:flex; align-items:flex-start; gap:12px;">
                                <div style="width:32px; height:32px; background:rgba(232,255,0,0.1); border-radius:8px; display:flex; align-items:center; justify-content:center; font-family:var(--font-display); font-size:16px; color:var(--primary); flex-shrink:0;">1</div>
                                <div style="font-size:14px; color:var(--text-muted);">Arrive at <strong style="color:var(--light);">Daiman Sports Complex</strong> at your scheduled time</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="display:flex; align-items:flex-start; gap:12px;">
                                <div style="width:32px; height:32px; background:rgba(232,255,0,0.1); border-radius:8px; display:flex; align-items:center; justify-content:center; font-family:var(--font-display); font-size:16px; color:var(--primary); flex-shrink:0;">2</div>
                                <div style="font-size:14px; color:var(--text-muted);">Show this <strong style="color:var(--light);">QR code</strong> to the court staff or at the scanner</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="display:flex; align-items:flex-start; gap:12px;">
                                <div style="width:32px; height:32px; background:rgba(232,255,0,0.1); border-radius:8px; display:flex; align-items:center; justify-content:center; font-family:var(--font-display); font-size:16px; color:var(--primary); flex-shrink:0;">3</div>
                                <div style="font-size:14px; color:var(--text-muted);">Access is granted once your QR is verified. <strong style="color:var(--light);">Enjoy your game!</strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="d-flex gap-3 flex-wrap mt-4">
                    <a href="{{ route('booking.history') }}" class="btn-primary-sm" style="padding:14px 28px; font-size:14px;">
                        <i class="fas fa-list"></i> View My Bookings
                    </a>
                    <a href="{{ route('booking.index') }}" class="btn-outline-sm" style="padding:14px 28px; font-size:14px;">
                        <i class="fas fa-plus"></i> Book Another Court
                    </a>
                    <a href="{{ route('home') }}" class="btn-outline-sm" style="padding:14px 28px; font-size:14px;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
