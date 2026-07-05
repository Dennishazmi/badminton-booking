@extends('layouts.admin')

@section('title', 'Reports — Admin')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-5">
    <div>
        <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">REPORTS</div>
        <div style="color:var(--text-muted); font-size:14px; margin-top:6px;">Usage analytics and revenue insights</div>
    </div>
    <!-- Period Selector -->
    <div style="display:flex; gap:8px;">
        @foreach([7 => '7D', 30 => '30D', 90 => '90D'] as $days => $label)
        <a href="{{ route('admin.reports', ['period' => $days]) }}"
           style="padding:8px 18px; border-radius:100px; font-size:13px; font-weight:700; text-decoration:none;
                  {{ $period == $days ? 'background:var(--primary); color:var(--dark);' : 'background:rgba(255,255,255,0.06); color:var(--text-muted);' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Bookings Over Time -->
    <div class="col-lg-8">
        <div class="stat-card">
            <div style="font-size:16px; font-weight:700; margin-bottom:24px;">
                <i class="fas fa-chart-line me-2" style="color:var(--primary);"></i>
                Revenue & Bookings (Last {{ $period }} Days)
            </div>
            <canvas id="revenueChart" style="max-height:280px;"></canvas>
        </div>
    </div>

    <!-- Payment Methods Breakdown -->
    <div class="col-lg-4">
        <div class="stat-card">
            <div style="font-size:16px; font-weight:700; margin-bottom:24px;">
                <i class="fas fa-chart-pie me-2" style="color:var(--primary);"></i>
                Payment Methods
            </div>
            <canvas id="methodChart" style="max-height:220px;"></canvas>
            <div class="mt-3 d-flex flex-column gap-2">
                @foreach($bookingsByMethod as $method)
                <div style="display:flex; align-items:center; justify-content:space-between; font-size:13px;">
                    <span style="color:var(--text-muted);">{{ ucfirst(str_replace('_',' ', $method->payment_method)) }}</span>
                    <span style="font-weight:700;">{{ $method->count }} <span style="color:var(--primary);">·</span> RM {{ number_format($method->total, 0) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Court Performance -->
    <div class="col-lg-6">
        <div class="stat-card">
            <div style="font-size:16px; font-weight:700; margin-bottom:24px;">
                <i class="fas fa-table-tennis me-2" style="color:var(--primary);"></i>
                Court Performance
            </div>
            @foreach($bookingsByCourt as $court)
            <div style="margin-bottom:18px;">
                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;">
                    <span style="font-weight:600;">{{ $court->name }}</span>
                    <span style="color:var(--primary); font-weight:700;">{{ $court->bookings_count }} bookings</span>
                </div>
                @php
                    $max = $bookingsByCourt->max('bookings_count') ?: 1;
                    $pct = $max > 0 ? ($court->bookings_count / $max * 100) : 0;
                @endphp
                <div style="height:8px; background:var(--dark-4); border-radius:4px; overflow:hidden;">
                    <div style="height:100%; width:{{ $pct }}%; background:var(--primary); border-radius:4px; transition:width 0.8s ease;"></div>
                </div>
                <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">
                    Revenue: RM {{ number_format($court->bookings_sum_amount ?? 0, 2) }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Top Users -->
    <div class="col-lg-6">
        <div class="stat-card">
            <div style="font-size:16px; font-weight:700; margin-bottom:24px;">
                <i class="fas fa-trophy me-2" style="color:var(--primary);"></i>
                Top Players
            </div>
            @forelse($topUsers as $i => $user)
            <div style="display:flex; align-items:center; gap:14px; padding:12px 0; border-bottom:1px solid var(--border);">
                <div style="width:28px; height:28px; border-radius:50%;
                    background:var(--dark-4);
		    color:var(--light);
	      	    font-weight:{{ $i < 3 ? '700' : '400' }};
                    display:flex; align-items:center; justify-content:center;
                    font-family:var(--font-display); font-size:14px; flex-shrink:0;">
                    {{ $i + 1 }}
                </div>
                <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=ffffff&color=000000&size=32&bold=true"
                    style="width:32px; height:32px; border-radius:50%; flex-shrink:0;" alt="">
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:14px;">{{ $user->name }}</div>
                    <div style="font-size:12px; color:var(--text-muted);">{{ $user->email }}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-family:var(--font-display); font-size:18px; color:var(--primary);">{{ $user->bookings_count }}</div>
                    <div style="font-size:11px; color:var(--text-muted);">RM {{ number_format($user->bookings_sum_amount ?? 0, 0) }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:40px; color:var(--text-muted);">No data yet</div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#888';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
Chart.defaults.font.family = "'DM Sans', sans-serif";

// Revenue / Bookings Chart
const days   = @json($bookingsByDay->pluck('date'));
const counts = @json($bookingsByDay->pluck('count'));
const revenue= @json($bookingsByDay->pluck('revenue'));

new Chart(document.getElementById('revenueChart'), {
    data: {
        labels: days,
        datasets: [
            {
                type: 'bar',
                label: 'Revenue (RM)',
                data: revenue,
                backgroundColor: 'rgba(0,230,118,0.25)',
                borderColor: 'rgba(0,230,118,0.6)',
                borderWidth: 1,
                yAxisID: 'y1',
            },
            {
                type: 'line',
                label: 'Bookings',
                data: counts,
                borderColor: '#00d68f',
                backgroundColor: 'rgba(0,214,143,0.08)',
                borderWidth: 2,
                pointBackgroundColor: '#00d68f',
                pointRadius: 3,
                tension: 0.4,
                fill: true,
                yAxisID: 'y',
            },
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { labels: { color: '#aaa', font: { size: 12 } } } },
        scales: {
            x:  { ticks: { maxTicksLimit: 10, color: '#666', font: { size: 11 } } },
            y:  { position: 'left',  ticks: { color: '#00d68f', font: { size: 11 } }, title: { display: true, text: 'Bookings', color: '#00d68f' } },
            y1: { position: 'right', ticks: { color: 'rgba(0,230,118,0.8)', font: { size: 11 }, callback: v => 'RM ' + v }, grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue (RM)', color: 'rgba(0,230,118,0.8)' } },
        }
    }
});

// Payment Method Donut
const methods = @json($bookingsByMethod->pluck('payment_method'));
const mCounts = @json($bookingsByMethod->pluck('count'));
new Chart(document.getElementById('methodChart'), {
    type: 'doughnut',
    data: {
        labels: methods.map(m => m.replace('_',' ').replace(/\b\w/g, l => l.toUpperCase())),
        datasets: [{
            data: mCounts,
            backgroundColor: ['rgba(0,230,118,0.8)', 'rgba(0,214,143,0.8)', 'rgba(255,184,0,0.8)', 'rgba(0,180,216,0.8)'],
            borderColor: 'var(--dark-3)',
            borderWidth: 3,
        }]
    },
    options: {
        cutout: '65%',
        plugins: { legend: { display: false } },
    }
});
</script>
@endpush
