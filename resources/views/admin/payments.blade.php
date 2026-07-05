@extends('layouts.admin')

@section('title', 'Payments — Admin')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-5">
    <div>
        <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">PAYMENTS</div>
        <div style="color:var(--text-muted); font-size:14px; margin-top:6px;">Revenue tracking and payment history</div>
    </div>
</div>

<!-- Revenue Summary -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon icon-green"><i class="fas fa-coins"></i></div>
            <div class="stat-card-value">RM {{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-card-label">Total Revenue</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon icon-yellow"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-card-value">RM {{ number_format($todayRevenue, 2) }}</div>
            <div class="stat-card-label">Today's Revenue</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon icon-blue"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-card-value">RM {{ number_format($monthRevenue, 2) }}</div>
            <div class="stat-card-label">This Month's Revenue</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="stat-card mb-4">
    <form method="GET" action="{{ route('admin.payments') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Payment Method</label>
            <select name="method" class="form-select">
                <option value="">All Methods</option>
                <option value="credit_card" {{ request('method') === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                <option value="fpx"         {{ request('method') === 'fpx'         ? 'selected' : '' }}>FPX</option>
                <option value="ewallet"     {{ request('method') === 'ewallet'     ? 'selected' : '' }}>E-Wallet</option>
                <option value="paypal"      {{ request('method') === 'paypal'      ? 'selected' : '' }}>PayPal</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Date From</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Date To</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn-primary-sm w-100 justify-content-center" style="padding:13px;">
                <i class="fas fa-search"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Payments Table -->
<div class="stat-card">
    <div class="table-dark-custom">
        <table>
            <thead>
                <tr>
                    <th>Booking #</th>
                    <th>Player</th>
                    <th>Court</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Paid On</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td style="color:var(--primary); font-weight:700; font-size:13px;">
                        <a href="{{ route('admin.bookings.show', $payment->id) }}" style="color:inherit; text-decoration:none;">
                            #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                        </a>
                    </td>
                    <td>
                        <div style="font-weight:600; font-size:13px;">{{ $payment->user->name }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $payment->user->email }}</div>
                    </td>
                    <td style="font-size:13px;">{{ $payment->court->name }}</td>
                    <td style="font-size:13px; color:var(--text-muted);">{{ \Carbon\Carbon::parse($payment->booking_date)->format('d M Y') }}</td>
                    <td>
                        <span style="font-size:12px; padding:4px 10px; border-radius:100px; background:rgba(255,255,255,0.06); color:#aaa;">
                            @switch($payment->payment_method)
                                @case('credit_card') <i class="fas fa-credit-card me-1"></i>Credit Card @break
                                @case('fpx')         <i class="fas fa-university me-1"></i>FPX @break
                                @case('ewallet')     <i class="fas fa-mobile-alt me-1"></i>E-Wallet @break
                                @case('paypal')      <i class="fab fa-paypal me-1"></i>PayPal @break
                                @default             {{ ucfirst($payment->payment_method ?? '—') }}
                            @endswitch
                        </span>
                    </td>
                    <td style="color:var(--success); font-weight:700; font-size:15px;">RM {{ number_format($payment->amount, 2) }}</td>
                    <td style="font-size:12px; color:var(--text-muted);">{{ $payment->updated_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:60px; color:var(--text-muted);">No payments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $payments->links() }}
    </div>
</div>

@endsection
