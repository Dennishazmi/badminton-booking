@extends('layouts.admin')

@section('title', 'Users — Admin')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-5">
    <div>
        <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">USERS</div>
        <div style="color:var(--text-muted); font-size:14px; margin-top:6px;">{{ $users->total() }} registered users</div>
    </div>
    <!-- Search -->
    <form method="GET" action="{{ route('admin.users') }}" style="display:flex; gap:8px;">
        <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}" style="width:240px; padding:10px 14px; font-size:14px;">
        <button type="submit" class="btn-primary-sm" style="padding:10px 18px;"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="stat-card">
    <div class="table-dark-custom">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Bookings</th>
                    <th>Total Spent</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=ffffff&color=000000&size=36&bold=true"
                                style="width:36px; height:36px; border-radius:50%; border:2px solid rgba(0,230,118,0.2);" alt="">
                            <div>
                                <div style="font-weight:600; font-size:14px;">{{ $user->name }}</div>
                                <div style="font-size:12px; color:var(--text-muted);">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--text-muted); font-size:13px;">{{ $user->phone ?? '—' }}</td>
                    <td>
                        <span style="padding:4px 10px; border-radius:100px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;
                            {{ $user->role === 'admin' ? 'background:rgba(0,230,118,0.1); color:var(--primary);' : 'background:rgba(255,255,255,0.06); color:#aaa;' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->bookings_count }}</td>
                    <td style="color:var(--primary); font-weight:600;">RM {{ number_format($user->total_spent ?? 0, 2) }}</td>
                    <td style="font-size:13px; color:var(--text-muted);">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn-outline-sm" style="padding:5px 10px; font-size:11px;" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.toggle-role', $user->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="btn-outline-sm" style="padding:5px 10px; font-size:11px;" title="{{ $user->role === 'admin' ? 'Remove Admin' : 'Make Admin' }}">
                                    <i class="fas fa-{{ $user->role === 'admin' ? 'user-minus' : 'user-shield' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn-danger-sm" style="padding:5px 10px; font-size:11px;" title="Delete"
                                    data-confirm="Delete user {{ $user->name }}? This cannot be undone.">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);">
                        @if(request('search'))
                            No users found matching "{{ request('search') }}"
                        @else
                            No users registered yet
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
</div>

@endsection
