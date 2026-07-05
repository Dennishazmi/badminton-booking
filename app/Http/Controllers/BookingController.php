<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
    // All available time slots (hourly, 7am–10pm)
    protected array $allSlots = [
        '08:00 – 09:00', '09:00 – 10:00', '10:00 – 11:00',
        '11:00 – 12:00', '12:00 – 13:00', '13:00 – 14:00',
        '14:00 – 15:00', '15:00 – 16:00', '16:00 – 17:00',
        '17:00 – 18:00', '18:00 – 19:00', '19:00 – 20:00',
        '20:00 – 21:00', '21:00 – 22:00', '22:00 – 23:00',
        '23:00 – 00:00', '00:00 – 01:00',
    ];

    public function index(Request $request)
    {
        $courts    = Court::all();
        $date      = $request->input('date', today()->format('Y-m-d'));
        $courtId   = $request->input('court');

        // Build time slots with availability info
        $timeSlots = $this->buildTimeSlots($date, $courtId);

        return view('user.booking-index', compact('courts', 'timeSlots'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'court_id'     => 'required|exists:courts,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'time_slot'    => 'required|string',
            'notes'        => 'nullable|string|max:500',
        ]);

        $court = Court::findOrFail($data['court_id']);

        // Check availability
        if ($court->isBookedAt($data['booking_date'], $data['time_slot'])) {
            return back()->withErrors(['time_slot' => 'This slot is already booked. Please choose another.'])->withInput();
        }

        if (!$court->is_available) {
            return back()->withErrors(['court_id' => 'This court is currently unavailable.'])->withInput();
        }

        // Create pending booking
        $booking = Booking::create([
            'user_id'        => Auth::id(),
            'court_id'       => $court->id,
            'booking_date'   => $data['booking_date'],
            'time_slot'      => $data['time_slot'],
            'status'         => 'pending',
            'amount'         => $court->price_per_hour,
            'payment_status' => 'unpaid',
            'notes'          => $data['notes'] ?? null,
        ]);

        return redirect()->route('payment.show', $booking->id);
    }

    public function show(int $id)
    {
        $booking = Booking::with(['court', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $qrCode = QrCode::format('svg')
            ->size(220)
            ->errorCorrection('H')
            ->generate(
                route('admin.qr-verify', $booking->qr_token)
            );

        return view('user.booking-confirmed', compact('booking', 'qrCode'));
    }

    public function history(Request $request)
    {
        $query = Booking::with('court')
            ->where('user_id', Auth::id())
            ->orderByDesc('booking_date')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $totalCount = Booking::where('user_id', Auth::id())->count();
        $bookings   = $query->paginate(10)->withQueryString();

        return view('user.booking-history', compact('bookings', 'totalCount'));
    }

    public function cancel(int $id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($booking->status, ['confirmed', 'pending'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        if ($booking->booking_date->isPast()) {
            return back()->with('error', 'Cannot cancel a past booking.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking #' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . ' has been cancelled.');
    }

    public function downloadQr(int $id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        $qrCode = QrCode::format('png')
            ->size(400)
            ->errorCorrection('H')
            ->generate(route('admin.qr-verify', $booking->qr_token));

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="booking-' . $booking->qr_token . '.png"');
    }

    public function getSlots(Request $request)
    {
        $request->validate([
            'date'     => 'required|date',
            'court_id' => 'required|exists:courts,id',
        ]);

        return response()->json(
            $this->buildTimeSlots($request->date, $request->court_id)
        );
    }

    private function buildTimeSlots(string $date, ?int $courtId): array
    {
        $bookedSlots = [];

        if ($courtId) {
            $bookedSlots = Booking::where('court_id', $courtId)
                ->where('booking_date', $date)
                ->whereIn('status', ['confirmed', 'pending'])
                ->pluck('time_slot')
                ->toArray();
        }

        return array_map(fn($slot) => [
            'time'   => $slot,
            'booked' => in_array($slot, $bookedSlots),
        ], $this->allSlots);
    }
}
