<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => User::count(),
                'owners' => User::where('role', 'owner')->count(),
                'offices' => Office::count(),
                'bookings' => Booking::count(),
            ],
            'recentUsers' => User::latest()->take(6)->get()->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]),
            'recentBookings' => Booking::with(['office', 'user'])->latest()->take(6)->get()->map(fn (Booking $booking) => [
                'id' => $booking->id,
                'office' => $booking->office?->title,
                'user' => $booking->user?->name,
                'status' => $booking->status,
                'total_amount' => $booking->total_amount / 100,
            ]),
        ]);
    }
}
