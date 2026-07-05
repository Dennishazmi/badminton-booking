@extends('layouts.app')

@section('title', 'Book a Court — Daiman Sports Complex')

@section('content')

<div class="page-header">
    <div class="container">
        <div class="page-title">BOOK A COURT</div>
        <div class="page-subtitle"><i class="fas fa-map-marker-alt me-2" style="color:var(--primary);"></i>Daiman Sports Complex, Johor Bahru</div>
    </div>
</div>

<div class="section section-dark">
    <div class="container">
        <form action="{{ route('booking.store') }}" method="POST" id="booking-form">
            @csrf
            <input type="hidden" name="court_id" id="selected_court_id" value="{{ request('court') }}">
            <input type="hidden" name="time_slot" id="selected_time">

            <div class="row g-4">
                <!-- Main Form -->
                <div class="col-lg-8">

                    <!-- Step 1: Select Court -->
                    <div class="booking-step">
                        <div class="step-header">
                            <div class="step-number">1</div>
                            <div class="step-title">Select a Court</div>
                        </div>
                        <div class="row g-3">
                            @foreach($courts as $court)
                            <div class="col-md-6">
                                <div class="court-select-card {{ request('court') == $court->id ? 'selected' : '' }}"
                                    data-court-id="{{ $court->id }}"
                                    data-court-name="{{ $court->name }}"
                                    data-court-price="{{ $court->price_per_hour }}">
                                    <div class="d-flex align-items-start gap-3">
                                        <div style="width:48px; height:48px; background:rgba(232,255,0,0.1); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:22px; color:var(--primary); flex-shrink:0;">
                                            🏸
                                        </div>
                                        <div>
                                            <div style="font-weight:700; font-size:16px; margin-bottom:4px;">{{ $court->name }}</div>
                                            <div style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">
                                                <span style="background:rgba(255,255,255,0.06); padding:2px 8px; border-radius:100px;">{{ $court->type }}</span>
                                            </div>
                                            <div style="font-family:var(--font-display); font-size:22px; color:var(--primary);">
                                                RM {{ number_format($court->price_per_hour, 2) }} <span style="font-family:var(--font-body); font-size:13px; color:var(--text-muted); font-weight:400;">/hr</span>
                                            </div>
                                        </div>
                                        <div class="ms-auto">
                                            @if($court->is_available)
                                                <span style="width:10px; height:10px; background:var(--success); border-radius:50%; display:block; box-shadow:0 0 0 3px rgba(0,214,143,0.2);"></span>
                                            @else
                                                <span style="width:10px; height:10px; background:var(--danger); border-radius:50%; display:block;"></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Step 2: Select Date -->
                    <div class="booking-step">
                        <div class="step-header">
                            <div class="step-number">2</div>
                            <div class="step-title">Choose Date</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Booking Date</label>
                                <input type="date" id="booking_date" name="booking_date"
                                    class="form-control @error('booking_date') is-invalid @enderror"
                                    value="{{ old('booking_date', date('Y-m-d')) }}" required>
                                @error('booking_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Select Time Slot -->
                    <div class="booking-step">
                        <div class="step-header">
                            <div class="step-number">3</div>
                            <div class="step-title">Select Time Slot <span style="font-size:14px; font-weight:400; color:var(--text-muted);">(1 hour sessions)</span></div>
                        </div>
                        <div class="row g-2" id="time-slots-container">
                            @foreach($timeSlots as $slot)
                            <div class="col-6 col-md-3 col-lg-2">
                                <div class="time-slot {{ $slot['booked'] ? 'booked' : '' }}"
                                    data-time="{{ $slot['time'] }}"
                                    {{ $slot['booked'] ? 'title=Already booked' : '' }}>
                                    {{ $slot['time'] }}
                                    @if($slot['booked'])
                                        <div style="font-size:10px; opacity:0.5;">Booked</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('time_slot')
                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Step 4: Notes (optional) -->
                    <div class="booking-step">
                        <div class="step-header">
                            <div class="step-number">4</div>
                            <div class="step-title">Additional Notes <span style="font-size:14px; font-weight:400; color:var(--text-muted);">(optional)</span></div>
                        </div>
                        <textarea name="notes" class="form-control" rows="3"
                            placeholder="Any special requests? E.g. need extra equipment, accessibility needs...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <div class="summary-title">Booking Summary</div>

                        <div class="summary-row">
                            <span class="label">Player</span>
                            <span class="value">{{ auth()->user()->name }}</span>
                        </div>

                        <div class="summary-row">
                            <span class="label">Court</span>
                            <span class="value" id="summary-court">—</span>
                        </div>

                        <div class="summary-row">
                            <span class="label">Date</span>
                            <span class="value" id="summary-date">—</span>
                        </div>

                        <div class="summary-row">
                            <span class="label">Time</span>
                            <span class="value" id="summary-time">—</span>
                        </div>

                        <div class="summary-row">
                            <span class="label">Duration</span>
                            <span class="value">1 Hour</span>
                        </div>

                        <hr class="summary-divider">

                        <div class="summary-row">
                            <span class="label">Court Fee</span>
                            <span class="value" id="summary-price">—</span>
                        </div>

                        <hr class="summary-divider">

                        <div class="summary-total">
                            <span>Total</span>
                            <span class="amount" id="summary-total">—</span>
                        </div>

                        <button type="submit" class="btn-submit" style="margin-top:16px;">
                            <i class="fas fa-arrow-right me-2"></i>Proceed to Payment
                        </button>

                        <div style="text-align:center; margin-top:16px; font-size:12px; color:var(--text-muted);">
                            <i class="fas fa-lock me-1"></i> Secure payment via encrypted gateway
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
