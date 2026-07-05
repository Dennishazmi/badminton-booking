<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminController extends Controller
{
    // ─────────────────────────────────────
    //  DASHBOARD
    // ─────────────────────────────────────
    public function dashboard()
    {
        $stats = [
            'total_bookings'   => Booking::count(),
            'bookings_today'   => Booking::whereDate('created_at', today())->count(),
            'total_revenue'    => Booking::where('payment_status', 'paid')->sum('amount'),
            'revenue_today'    => Booking::where('payment_status', 'paid')->whereDate('created_at', today())->sum('amount'),
            'total_users'      => User::where('role', 'user')->count(),
            'new_users_week'   => User::where('role', 'user')->where('created_at', '>=', now()->subWeek())->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
        ];

        $recentBookings = Booking::with(['user', 'court'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $courts = Court::withCount('bookings')->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'courts'));
    }

    // ─────────────────────────────────────
    //  BOOKINGS
    // ─────────────────────────────────────
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'court'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"));
        }
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->paginate(15)->withQueryString();
        $counts   = [
            'all'       => Booking::count(),
            'pending'   => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('admin.bookings', compact('bookings', 'counts'));
    }

    public function showBooking(int $id)
    {
        $booking = Booking::with(['user', 'court'])->findOrFail($id);

        $qrCode = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate(route('admin.qr-verify', $booking->qr_token));

        return view('admin.booking-show', compact('booking', 'qrCode'));
    }

    public function approveBooking(int $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'confirmed']);
        return back()->with('success', 'Booking #' . str_pad($id, 6, '0', STR_PAD_LEFT) . ' approved.');
    }

    public function rejectBooking(int $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking #' . str_pad($id, 6, '0', STR_PAD_LEFT) . ' rejected.');
    }

    public function destroyBooking(int $id)
    {
        Booking::findOrFail($id)->delete();
        return back()->with('success', 'Booking deleted.');
    }

    // ─────────────────────────────────────
    //  COURTS
    // ─────────────────────────────────────
    public function courts()
    {
        $courts = Court::withCount('bookings')->get();
        return view('admin.courts', compact('courts'));
    }

    public function createCourt()
    {
        return view('admin.court-form', ['court' => null]);
    }

    public function storeCourt(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'type'            => 'required|in:Indoor,Outdoor',
            'description'     => 'nullable|string|max:500',
            'price_per_hour'  => 'required|numeric|min:1|max:9999',
            'is_available'    => 'boolean',
        ]);

        $data['is_available'] = $request->boolean('is_available', true);
        Court::create($data);

        return redirect()->route('admin.courts')->with('success', 'Court "' . $data['name'] . '" created successfully.');
    }

    public function editCourt(int $id)
    {
        $court = Court::findOrFail($id);
        return view('admin.court-form', compact('court'));
    }

    public function updateCourt(Request $request, int $id)
    {
        $court = Court::findOrFail($id);

        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'type'           => 'required|in:Indoor,Outdoor',
            'description'    => 'nullable|string|max:500',
            'price_per_hour' => 'required|numeric|min:1|max:9999',
            'is_available'   => 'boolean',
        ]);

        $data['is_available'] = $request->boolean('is_available');
        $court->update($data);

        return redirect()->route('admin.courts')->with('success', 'Court updated successfully.');
    }

    public function toggleCourt(int $id)
    {
        $court = Court::findOrFail($id);
        $court->update(['is_available' => !$court->is_available]);
        $status = $court->is_available ? 'available' : 'blocked';
        return back()->with('success', "{$court->name} is now {$status}.");
    }

    public function destroyCourt(int $id)
    {
        $court = Court::findOrFail($id);
        // Check for upcoming bookings
        $upcoming = $court->bookings()
            ->where('booking_date', '>=', today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();

        if ($upcoming > 0) {
            return back()->with('error', "Cannot delete {$court->name} — it has {$upcoming} upcoming booking(s).");
        }

        $court->delete();
        return back()->with('success', "Court deleted.");
    }

    // ─────────────────────────────────────
    //  USERS
    // ─────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::withCount('bookings')
            ->withSum(['bookings' => fn($q) => $q->where('payment_status', 'paid')], 'amount')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }

        $users = $query->paginate(15)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function showUser(int $id)
    {
        $user     = User::findOrFail($id);
        $bookings = Booking::with('court')->where('user_id', $id)->orderByDesc('created_at')->paginate(10);
        return view('admin.user-show', compact('user', 'bookings'));
    }

    public function toggleUserRole(int $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }
        $user->update(['role' => $user->role === 'admin' ? 'user' : 'admin']);
        return back()->with('success', "{$user->name}'s role updated to " . ucfirst($user->role) . '.');
    }

    public function destroyUser(int $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account here.');
        }
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    // ─────────────────────────────────────
    //  PAYMENTS
    // ─────────────────────────────────────
    public function payments(Request $request)
    {
        $query = Booking::with(['user', 'court'])
            ->where('payment_status', 'paid')
            ->orderByDesc('created_at');

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments     = $query->paginate(15)->withQueryString();
        $totalRevenue = Booking::where('payment_status', 'paid')->sum('amount');
        $todayRevenue = Booking::where('payment_status', 'paid')->whereDate('created_at', today())->sum('amount');
        $monthRevenue = Booking::where('payment_status', 'paid')->whereMonth('created_at', now()->month)->sum('amount');

        return view('admin.payments', compact('payments', 'totalRevenue', 'todayRevenue', 'monthRevenue'));
    }

    // ─────────────────────────────────────
    //  REPORTS
    // ─────────────────────────────────────
    public function reports(Request $request)
    {
        $period = $request->input('period', '30');

        $bookingsByDay = Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as revenue')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $bookingsByCourt = Court::withCount(['bookings' => fn($q) => $q->where('status', 'confirmed')])
            ->withSum(['bookings' => fn($q) => $q->where('payment_status', 'paid')], 'amount')
            ->get();

        $bookingsByMethod = Booking::selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->where('payment_status', 'paid')
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        $topUsers = User::withCount(['bookings' => fn($q) => $q->where('status', 'confirmed')])
            ->withSum(['bookings' => fn($q) => $q->where('payment_status', 'paid')], 'amount')
            ->having('bookings_count', '>', 0)
            ->orderByDesc('bookings_count')
            ->take(10)
            ->get();

        return view('admin.reports', compact(
            'bookingsByDay', 'bookingsByCourt', 'bookingsByMethod', 'topUsers', 'period'
        ));
    }

    // ─────────────────────────────────────
    //  QR SCANNER
    // ─────────────────────────────────────
    public function qrScanner()
    {
        return view('admin.qr-scanner');
    }

    public function verifyQr(string $token)
    {
        $booking = Booking::with(['user', 'court'])
            ->where('qr_token', $token)
            ->first();

        if (!$booking) {
            return response()->json(['valid' => false, 'message' => 'QR code not found.'], 404);
        }

        if ($booking->status !== 'confirmed') {
            return response()->json([
                'valid'   => false,
                'message' => 'Booking is not confirmed. Status: ' . ucfirst($booking->status),
            ], 400);
        }

        if ($booking->booking_date->isPast() && $booking->booking_date->diffInDays(today()) > 0) {
            return response()->json(['valid' => false, 'message' => 'Booking date has passed.'], 400);
        }

        if ($booking->qr_scanned_at) {
            return response()->json([
                'valid'      => false,
                'already'    => true,
                'message'    => 'QR already scanned at ' . $booking->qr_scanned_at->format('H:i d M Y'),
                'booking'    => $this->bookingDetails($booking),
            ], 400);
        }

        // Mark as scanned
        $booking->update(['qr_scanned_at' => now()]);

        return response()->json([
            'valid'   => true,
            'message' => 'Access granted!',
            'booking' => $this->bookingDetails($booking),
        ]);
    }

    private function bookingDetails(Booking $booking): array
    {
        return [
            'id'           => str_pad($booking->id, 6, '0', STR_PAD_LEFT),
            'player'       => $booking->user->name,
            'email'        => $booking->user->email,
            'court'        => $booking->court->name,
            'date'         => $booking->booking_date->format('d M Y'),
            'time'         => $booking->time_slot,
            'amount'       => 'RM ' . number_format($booking->amount, 2),
            'scanned_at'   => $booking->qr_scanned_at?->format('H:i d M Y'),
        ];
    }
}
