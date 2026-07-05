@extends('layouts.admin')

@section('title', 'Courts — Admin')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-5">
    <div>
        <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">COURTS</div>
        <div style="color:var(--text-muted); font-size:14px; margin-top:6px;">Manage all badminton courts and schedules</div>
    </div>
    <a href="{{ route('admin.courts.create') }}" class="btn-primary-sm" style="padding:12px 24px;">
        <i class="fas fa-plus"></i> Add Court
    </a>
</div>

<div class="row g-4">
    @forelse($courts as $court)
    <div class="col-md-6 col-lg-4">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:48px; height:48px; background:rgba(232,255,0,0.1); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:22px; color:var(--primary);">
                        🏸
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:16px;">{{ $court->name }}</div>
                        <div style="font-size:12px; background:rgba(255,255,255,0.06); color:var(--text-muted); padding:2px 8px; border-radius:100px; margin-top:3px; display:inline-block;">{{ $court->type }}</div>
                    </div>
                </div>
                <div style="width:10px; height:10px; border-radius:50%; background:{{ $court->is_available ? 'var(--success)' : 'var(--danger)' }}; margin-top:6px; box-shadow:0 0 0 3px {{ $court->is_available ? 'rgba(0,214,143,0.2)' : 'rgba(255,61,87,0.2)' }};"></div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-top:1px solid var(--border); border-bottom:1px solid var(--border); margin-bottom:16px;">
                <div style="font-family:var(--font-display); font-size:26px; color:var(--primary);">RM {{ number_format($court->price_per_hour, 2) }}</div>
                <div style="font-size:13px; color:var(--text-muted);">per hour</div>
            </div>

            @if($court->description)
            <div style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">{{ $court->description }}</div>
            @endif

            <div style="display:flex; gap:8px; margin-bottom:16px;">
                <span style="font-size:12px; padding:4px 10px; border-radius:100px; background:rgba(255,255,255,0.06); color:#aaa;">
                    <i class="fas fa-calendar me-1"></i>{{ $court->bookings_count ?? $court->bookings()->count() }} bookings
                </span>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.courts.edit', $court->id) }}" class="btn-outline-sm flex-1 justify-content-center" style="font-size:13px; padding:9px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.courts.toggle', $court->id) }}" method="POST" style="flex:1;">
                    @csrf @method('PATCH')
                    <button class="w-100 {{ $court->is_available ? 'btn-danger-sm' : 'btn-primary-sm' }}" style="font-size:13px; padding:9px; justify-content:center;">
                        <i class="fas fa-{{ $court->is_available ? 'ban' : 'check' }}"></i>
                        {{ $court->is_available ? 'Block' : 'Unblock' }}
                    </button>
                </form>
                <form action="{{ route('admin.courts.destroy', $court->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn-danger-sm" style="font-size:13px; padding:9px;"
                        data-confirm="Delete {{ $court->name }}? All bookings will be affected.">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-table-tennis"></i></div>
            <div class="empty-title">No courts added yet</div>
            <div class="empty-desc" style="margin-bottom:24px;">Add your first badminton court to start accepting bookings.</div>
            <a href="{{ route('admin.courts.create') }}" class="btn-primary-sm" style="padding:14px 28px; font-size:15px;">
                <i class="fas fa-plus"></i> Add First Court
            </a>
        </div>
    </div>
    @endforelse
</div>

@endsection
