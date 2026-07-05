<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function show(int $bookingId)
    {
        $booking = Booking::with('court')
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($bookingId);

        return view('user.payment', compact('booking'));
    }

    public function process(Request $request)
    {
        $data = $request->validate([
            'booking_id'     => 'required|exists:bookings,id',
            'payment_method' => 'required|in:credit_card,fpx,ewallet,paypal',
        ]);

        $booking = Booking::with('court')
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($data['booking_id']);

        // Simulate payment processing
        // In production, integrate real payment gateway here (e.g. Stripe, iPay88, Billplz)
        $paymentSuccess = $this->simulatePayment($data['payment_method']);

        if ($paymentSuccess) {
            $booking->update([
                'status'         => 'confirmed',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'paid',
            ]);

            return redirect()->route('booking.show', $booking->id)
                ->with('success', 'Payment successful! Your court is confirmed.');
        }

        // Payment failed
        $booking->update(['status' => 'cancelled', 'payment_status' => 'failed']);

        return redirect()->route('booking.index')
            ->with('error', 'Payment failed. Please try again.');
    }

    // Simulate payment — always succeeds in demo mode
    // Replace with real gateway (e.g. Stripe) in production
    private function simulatePayment(string $method): bool
    {
        // Simulate slight processing delay
        return true;
    }
}
