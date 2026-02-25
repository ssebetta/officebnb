<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::query()
            ->with(['office', 'payment'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn (Booking $booking) => $this->serializeBooking($booking));

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function manage(Request $request)
    {
        abort_unless($request->user()?->isOwner(), 403);

        $bookings = Booking::query()
            ->with(['office', 'user', 'payment'])
            ->whereHas('office', function ($query) use ($request) {
                $query->where('owner_id', $request->user()->id);
            })
            ->latest()
            ->get()
            ->map(fn (Booking $booking) => $this->serializeBooking($booking, true));

        return Inertia::render('Bookings/Manage', [
            'bookings' => $bookings,
        ]);
    }

    public function store(Request $request, Office $office)
    {
        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'guests' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($office->owner_id === $request->user()->id) {
            return back()->withErrors(['start_date' => 'You cannot book your own office.']);
        }

        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);
        $days = $start->diffInDays($end) + 1;

        $overlap = Booking::query()
            ->where('office_id', $office->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($inner) use ($start, $end) {
                        $inner->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'The office is not available for those dates.']);
        }

        $totalAmount = $office->daily_rate * $days;

        Booking::create([
            'office_id' => $office->id,
            'user_id' => $request->user()->id,
            'start_date' => $start,
            'end_date' => $end,
            'guests' => $data['guests'],
            'total_amount' => $totalAmount,
            'currency' => 'USD',
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking request submitted.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        abort_unless($request->user()?->isAdmin() || $booking->office?->owner_id === $request->user()?->id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,cancelled'],
        ]);

        $booking->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Booking status updated.');
    }

    private function serializeBooking(Booking $booking, bool $includeGuest = false): array
    {
        return [
            'id' => $booking->id,
            'office' => $booking->office ? [
                'slug' => $booking->office->slug,
                'title' => $booking->office->title,
                'city' => $booking->office->city,
                'country' => $booking->office->country,
            ] : null,
            'guest' => $includeGuest && $booking->user ? [
                'id' => $booking->user->id,
                'name' => $booking->user->name,
                'email' => $booking->user->email,
            ] : null,
            'start_date' => $booking->start_date?->toDateString(),
            'end_date' => $booking->end_date?->toDateString(),
            'guests' => $booking->guests,
            'total_amount' => $booking->total_amount / 100,
            'currency' => $booking->currency,
            'status' => $booking->status,
            'notes' => $booking->notes,
            'payment' => $booking->payment ? [
                'status' => $booking->payment->status,
                'paid_at' => optional($booking->payment->paid_at)->toDateString(),
            ] : null,
        ];
    }
}
