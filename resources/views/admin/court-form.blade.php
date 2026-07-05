@extends('layouts.admin')

@section('title', ($court ? 'Edit Court' : 'Add Court') . ' — Admin')

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.courts') }}" style="color:var(--text-muted); text-decoration:none; font-size:14px; display:inline-flex; align-items:center; gap:8px; margin-bottom:16px;">
        <i class="fas fa-arrow-left"></i> Back to Courts
    </a>
    <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">
        {{ $court ? 'EDIT COURT' : 'ADD NEW COURT' }}
    </div>
    <div style="color:var(--text-muted); font-size:14px; margin-top:6px;">
        {{ $court ? 'Update court details and availability' : 'Add a new badminton court to the system' }}
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="stat-card">
            <form action="{{ $court ? route('admin.courts.update', $court->id) : route('admin.courts.store') }}" method="POST">
                @csrf
                @if($court) @method('PUT') @endif

                <div class="row g-4">
                    <div class="col-md-8">
                        <label class="form-label">Court Name <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $court?->name) }}"
                            placeholder="e.g. Court 1 — Champion Hall" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Court Type <span style="color:var(--danger);">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="Indoor"  {{ old('type', $court?->type) === 'Indoor'  ? 'selected' : '' }}>Indoor</option>
                            <option value="Outdoor" {{ old('type', $court?->type) === 'Outdoor' ? 'selected' : '' }}>Outdoor</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="3" placeholder="Brief description of the court facilities...">{{ old('description', $court?->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Price Per Hour (RM) <span style="color:var(--danger);">*</span></label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-weight:600; font-size:14px;">RM</span>
                            <input type="number" name="price_per_hour"
                                class="form-control @error('price_per_hour') is-invalid @enderror"
                                style="padding-left:44px;"
                                value="{{ old('price_per_hour', $court?->price_per_hour ?? 20) }}"
                                min="1" max="9999" step="0.50" required>
                        </div>
                        @error('price_per_hour')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Availability</label>
                        <div style="display:flex; align-items:center; gap:12px; margin-top:12px;">
                            <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                                <input type="hidden" name="is_available" value="0">
                                <input type="checkbox" name="is_available" value="1"
                                    style="width:20px; height:20px; accent-color:var(--primary);"
                                    {{ old('is_available', $court?->is_available ?? true) ? 'checked' : '' }}>
                                <span style="font-size:14px; font-weight:500;">Court is available for booking</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="separator">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn-submit" style="width:auto; padding:14px 36px; font-size:14px;">
                                <i class="fas fa-{{ $court ? 'save' : 'plus' }} me-2"></i>
                                {{ $court ? 'Save Changes' : 'Create Court' }}
                            </button>
                            <a href="{{ route('admin.courts') }}" class="btn-outline-sm" style="padding:14px 28px; font-size:14px;">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($court)
    <div class="col-lg-4">
        <div class="stat-card">
            <div style="font-size:14px; font-weight:700; margin-bottom:20px;">Court Statistics</div>
            <div class="summary-row" style="padding:10px 0; border-bottom:1px solid var(--border);">
                <span style="color:var(--text-muted); font-size:13px;">Total Bookings</span>
                <span style="font-weight:700;">{{ $court->bookings()->count() }}</span>
            </div>
            <div class="summary-row" style="padding:10px 0; border-bottom:1px solid var(--border);">
                <span style="color:var(--text-muted); font-size:13px;">Confirmed</span>
                <span style="font-weight:700; color:var(--success);">{{ $court->bookings()->where('status','confirmed')->count() }}</span>
            </div>
            <div class="summary-row" style="padding:10px 0; border-bottom:1px solid var(--border);">
                <span style="color:var(--text-muted); font-size:13px;">Upcoming</span>
                <span style="font-weight:700; color:var(--primary);">{{ $court->bookings()->where('booking_date','>=',today())->whereIn('status',['confirmed','pending'])->count() }}</span>
            </div>
            <div class="summary-row" style="padding:10px 0;">
                <span style="color:var(--text-muted); font-size:13px;">Revenue Generated</span>
                <span style="font-weight:700; color:var(--primary); font-family:var(--font-display); font-size:20px;">
                    RM {{ number_format($court->bookings()->where('payment_status','paid')->sum('amount'), 2) }}
                </span>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
