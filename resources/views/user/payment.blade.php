@extends('layouts.app')

@section('title', 'Payment — Daiman Sports Complex')

@section('content')

<div class="page-header">
    <div class="container">
        <div class="page-title">PAYMENT</div>
        <div class="page-subtitle">Complete your booking securely</div>
    </div>
</div>

<div class="section section-dark">
    <div class="container">
        <form action="{{ route('payment.process') }}" method="POST" id="payment-form">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
            <input type="hidden" name="payment_method" id="payment_method" value="">

            <div class="row g-4">
                <div class="col-lg-7">
                    <!-- Payment Methods -->
                    <div class="booking-step">
                        <div class="step-header">
                            <div class="step-number">1</div>
                            <div class="step-title">Select Payment Method</div>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="payment-method-card" data-method="credit_card" onclick="selectPayment(this)">
                                <div class="payment-icon card"><i class="fas fa-credit-card"></i></div>
                                <div>
                                    <div style="font-weight:700; font-size:15px;">Credit / Debit Card</div>
                                    <div style="font-size:13px; color:var(--text-muted);">Visa, Mastercard, American Express</div>
                                </div>
                                <div class="ms-auto" style="display:flex; gap:6px;">
                                    <img src="https://img.icons8.com/color/32/000000/visa.png" alt="Visa" style="height:22px;">
                                    <img src="https://img.icons8.com/color/32/000000/mastercard.png" alt="MC" style="height:22px;">
                                </div>
                            </div>

                            <div class="payment-method-card" data-method="fpx" onclick="selectPayment(this)">
                                <div class="payment-icon ewallet" style="background:rgba(232,255,0,0.15); color:var(--primary);"><i class="fas fa-university"></i></div>
                                <div>
                                    <div style="font-weight:700; font-size:15px;">FPX Online Banking</div>
                                    <div style="font-size:13px; color:var(--text-muted);">Maybank, CIMB, Public Bank & more</div>
                                </div>
                                <span style="margin-left:auto; background:rgba(0,214,143,0.1); color:var(--success); font-size:11px; font-weight:700; padding:3px 10px; border-radius:100px; border:1px solid rgba(0,214,143,0.2);">POPULAR</span>
                            </div>

                            <div class="payment-method-card" data-method="ewallet" onclick="selectPayment(this)">
                                <div class="payment-icon ewallet"><i class="fas fa-mobile-alt"></i></div>
                                <div>
                                    <div style="font-weight:700; font-size:15px;">E-Wallet</div>
                                    <div style="font-size:13px; color:var(--text-muted);">Touch 'n Go eWallet, GrabPay, Boost</div>
                                </div>
                            </div>

                            <div class="payment-method-card" data-method="paypal" onclick="selectPayment(this)">
                                <div class="payment-icon paypal"><i class="fab fa-paypal"></i></div>
                                <div>
                                    <div style="font-weight:700; font-size:15px;">PayPal</div>
                                    <div style="font-size:13px; color:var(--text-muted);">Pay with your PayPal account</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Details (shown when card selected) -->
                    <div class="booking-step" id="card-details" style="display:none;">
                        <div class="step-header">
                            <div class="step-number">2</div>
                            <div class="step-title">Card Details</div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Cardholder Name</label>
                                <input type="text" class="form-control" name="card_name" placeholder="Name on card" value="{{ auth()->user()->name }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" name="card_expiry" placeholder="MM / YY" maxlength="7">
                            </div>
                            <div class="col-6">
                                <label class="form-label">CVV</label>
                                <input type="text" class="form-control" name="card_cvv" placeholder="123" maxlength="4">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="col-lg-5">
                    <div class="order-summary">
                        <div class="summary-title"><i class="fas fa-receipt me-2" style="color:var(--primary);"></i>Order Summary</div>

                        <div style="background:var(--dark-4); border-radius:var(--radius-sm); padding:16px; margin-bottom:20px; border:1px solid var(--border);">
                            <div style="font-weight:700; font-size:16px; margin-bottom:8px;">{{ $booking->court->name }}</div>
                            <div style="font-size:13px; color:var(--text-muted);">
                                <div><i class="fas fa-calendar me-2" style="color:var(--primary);"></i>{{ \Carbon\Carbon::parse($booking->booking_date)->format('D, d M Y') }}</div>
                                <div class="mt-1"><i class="fas fa-clock me-2" style="color:var(--primary);"></i>{{ $booking->time_slot }}</div>
                                <div class="mt-1"><i class="fas fa-user me-2" style="color:var(--primary);"></i>{{ auth()->user()->name }}</div>
                            </div>
                        </div>

                        <div class="summary-row">
                            <span class="label">Court Fee (1 hour)</span>
                            <span class="value">RM {{ number_format($booking->court->price_per_hour, 2) }}</span>
                        </div>

                        <div class="summary-row">
                            <span class="label">Processing Fee</span>
                            <span class="value">RM 0.00</span>
                        </div>

                        <hr class="summary-divider">

                        <div class="summary-total">
                            <span>Total</span>
                            <span class="amount">RM {{ number_format($booking->amount, 2) }}</span>
                        </div>

                        <button type="submit" class="btn-submit" id="pay-btn" disabled>
                            <i class="fas fa-lock me-2"></i>Pay RM {{ number_format($booking->amount, 2) }}
                        </button>

                        <div style="text-align:center; margin-top:14px; font-size:12px; color:var(--text-muted);">
                            <i class="fas fa-shield-alt me-1"></i> 256-bit SSL encryption. Your payment is secure.
                        </div>

                        <hr class="summary-divider">

                        <a href="{{ route('booking.index') }}" class="btn-outline-sm w-100 justify-content-center">
                            <i class="fas fa-times me-1"></i> Cancel Booking
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function selectPayment(card) {
    document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('payment_method').value = card.dataset.method;
    document.getElementById('pay-btn').disabled = false;
    document.getElementById('card-details').style.display = card.dataset.method === 'credit_card' ? 'block' : 'none';
}

// Card number formatting
document.querySelector('[name="card_number"]')?.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let matches = v.match(/\d{4,16}/g);
    let match = matches && matches[0] || '';
    let parts = [];
    for (let i = 0, len = match.length; i < len; i += 4) {
        parts.push(match.substring(i, i + 4));
    }
    e.target.value = parts.length ? parts.join(' ') : v;
});
</script>
@endpush
