@extends('layouts.admin')

@section('title', 'User Detail — Admin')

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.users') }}" style="color:var(--text-muted); text-decoration:none; font-size:14px; display:inline-flex; align-items:center; gap:8px; margin-bottom:16px;">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
    <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">USER PROFILE</div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <!-- User Card -->
        <div class="stat-card text-center mb-4">
            <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=ffffff&color=000000&size=90&bold=true"
                style="width:90px; height:90px; border-radius:50%; border:3px solid rgba(0,230,118,0.3); margin-bottom:16px;" alt="">
            <div style="font-size:20px; font-weight:700;">{{ $user->name }}</div>
            <div style="color:var(--text-muted); font-size:14px; margin-top:4px;">{{ $user->email }}</div>
            @if($user->phone)
            <div style="color:var(--text-muted); font-size:13px; margin-top:2px;">{{ $user->phone }}</div>
            @endif
            <div style="margin-top:12px;">
                <span style="padding:5px 14px; border-radius:100px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;
                    {{ $user->role === 'admin' ? 'background:rgba(0,230,118,0.1); color:var(--primary);' : 'background:rgba(255,255,255,0.06); color:#aaa;' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
            <hr class="separator">
            <div style="display:flex; justify-content:space-around; text-align:center;">
                <div>
                    <div style="font-family:var(--font-display); font-size:28px; color:var(--primary);">{{ $user->bookings()->count() }}</div>
                    <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Bookings</div>
                </div>
                <div>
                    <div style="font-family:var(--font-display); font-size:28px; color:var(--primary);">RM {{ number_format($user->bookings()->where('payment_status','paid')->sum('amount'), 0) }}</div>
                    <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Spent</div>
                </div>
            </div>
            <hr class="separator">
            <div style="font-size:12px; color:var(--text-muted);">Member since {{ $user->created_at->format('d M Y') }}</div>
        </div>

        <!-- Actions -->
        <div class="stat-card">
            <div style="font-size:14px; font-weight:700; margin-bottom:16px;">Actions</div>
            <div class="d-flex flex-column gap-2">
                @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.toggle-role', $user->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="btn-outline-sm w-100 justify-content-center" style="padding:12px;">
                        <i class="fas fa-{{ $user->role === 'admin' ? 'user-minus' : 'user-shield' }} me-2"></i>
                        {{ $user->role === 'admin' ? 'Remove Admin Role' : 'Make Admin' }}
                    </button>
                </form>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn-danger-sm w-100 justify-content-center" style="padding:12px;"
                        data-confirm="Delete user {{ $user->name }}? All their bookings will also be deleted.">
                        <i class="fas fa-trash me-2"></i> Delete User
                    </button>
                </form>
                @else
                <div style="font-size:13px; color:var(--text-muted); text-align:center; padding:8px;">This is your own account</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="stat-card">
            <div style="font-size:16px; font-weight:700; margin-bottom:24px;">
                <i class="fas fa-history me-2" style="color:var(--primary);"></i>
                Booking History ({{ $bookings->total() }})
            </div>
            <div class="table-dark-custom">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Court</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td style="color:var(--primary); font-size:13px; font-weight:700;">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" style="color:inherit; text-decoration:none;">
                                    #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td style="font-size:13px;">{{ $booking->court->name }}</td>
                            <td style="font-size:13px; color:var(--text-muted);">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                            <td style="font-size:12px; color:var(--text-muted);">{{ $booking->time_slot }}</td>
                            <td style="font-size:13px; font-weight:700; color:var(--primary);">RM {{ number_format($booking->amount, 2) }}</td>
                            <td><span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">No bookings yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
