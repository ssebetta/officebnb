<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        if ($booking->status !== 'approved') {
            return back()->withErrors(['payment' => 'Booking must be approved before payment.']);
        }

        if ($booking->payment && $booking->payment->status === 'paid') {
            return back()->with('success', 'Payment already recorded.');
        }

        $data = $request->validate([
            'payment_method' => ['required', 'in:stripe,mobile_money,bank_transfer,pay_later'],
            'payment_details' => ['nullable', 'string', 'max:500'],
        ]);

        $status = $data['payment_method'] === 'pay_later' ? 'pending' : 'paid';
        $paidAt = $data['payment_method'] === 'pay_later' ? null : now();

        $booking->payment()->updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->total_amount,
                'currency' => $booking->currency,
                'status' => $status,
                'provider' => $data['payment_method'],
                'payment_method' => $data['payment_method'],
                'payment_details' => $data['payment_details'] ?? null,
                'reference' => $data['payment_method'] . '-' . now()->timestamp,
                'paid_at' => $paidAt,
            ]
        );

        $message = $data['payment_method'] === 'pay_later' 
            ? 'Booking confirmed. Payment due later.'
            : 'Payment recorded successfully.';

        return back()->with('success', $message);
    }
}
