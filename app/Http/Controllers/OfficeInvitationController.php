<?php

namespace App\Http\Controllers;

use App\Mail\OfficeInvitationMail;
use App\Models\Office;
use App\Models\OfficeInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OfficeInvitationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $invitations = OfficeInvitation::with(['office', 'inviter'])
            ->where('email', $user->email)
            ->whereNull('accepted_at')
            ->latest()
            ->get()
            ->map(fn (OfficeInvitation $invitation) => [
                'id' => $invitation->id,
                'office_title' => $invitation->office?->title,
                'invited_by' => $invitation->inviter?->name ?? 'OfficeBnB',
                'created_at' => $invitation->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Invitations/Index', [
            'invitations' => $invitations,
        ]);
    }

    public function store(Request $request, Office $office)
    {
        $user = $request->user();

        abort_unless($user?->isSuperAdmin() || $office->owner_id === $user?->id, 403);

        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $alreadyManager = $office->managers()->where('users.email', $data['email'])->exists();
        if ($alreadyManager) {
            return back()->with('error', 'That user already manages this office.');
        }

        $existingInvite = OfficeInvitation::where('office_id', $office->id)
            ->where('email', $data['email'])
            ->whereNull('accepted_at')
            ->first();

        if ($existingInvite) {
            return back()->with('error', 'An invitation is already pending for this email.');
        }

        $invitation = OfficeInvitation::create([
            'office_id' => $office->id,
            'email' => $data['email'],
            'token' => Str::random(40),
            'invited_by' => $user->id,
        ]);

        Mail::to($data['email'])->send(new OfficeInvitationMail($invitation));

        return back()->with('success', 'Invitation sent.');
    }

    public function accept(Request $request, OfficeInvitation $invitation)
    {
        $user = $request->user();

        abort_unless($user && $invitation->email === $user->email, 403);

        if (! $invitation->accepted_at) {
            $invitation->update(['accepted_at' => now()]);
            $invitation->office->managers()->syncWithoutDetaching([$user->id]);
        }

        return redirect()->route('dashboard')->with('success', 'Invitation accepted.');
    }

    public function decline(Request $request, OfficeInvitation $invitation)
    {
        $user = $request->user();

        abort_unless($user && $invitation->email === $user->email, 403);

        $invitation->delete();

        return redirect()->route('dashboard')->with('success', 'Invitation declined.');
    }

    public function showByToken(string $token)
    {
        $invitation = OfficeInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        $user = User::where('email', $invitation->email)->first();

        if ($user) {
            // User exists, redirect to login with the invitation token
            return redirect()->route('login', ['invitation_token' => $token])
                ->with('message', 'Please log in to accept the invitation.');
        }

        // User doesn't exist, show registration form
        return Inertia::render('Auth/RegisterFromInvitation', [
            'invitation' => [
                'token' => $invitation->token,
                'office_title' => $invitation->office?->title,
                'email' => $invitation->email,
            ],
        ]);
    }

    public function registerFromInvitation(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invitation_token' => ['required', 'string'],
        ]);

        $invitation = OfficeInvitation::where('token', $data['invitation_token'])
            ->whereNull('accepted_at')
            ->firstOrFail();

        // Find or create user
        $user = User::where('email', $invitation->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $invitation->email,
                'password' => Hash::make($data['password']),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);
        } else {
            // User exists but with placeholder data, update it
            $user->forceFill([
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ])->save();
        }

        // Accept invitation
        $invitation->update(['accepted_at' => now()]);
        $invitation->office->managers()->syncWithoutDetaching([$user->id]);

        // Log the user in
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration completed and invitation accepted.');
    }

    public function resend(Request $request, OfficeInvitation $invitation)
    {
        $user = $request->user();

        abort_unless($user?->isSuperAdmin() || $invitation->office->owner_id === $user?->id, 403);

        // Check if invitation is still pending
        if ($invitation->accepted_at) {
            return back()->with('error', 'This invitation has already been accepted.');
        }

        // Resend the email
        Mail::to($invitation->email)->send(new OfficeInvitationMail($invitation));

        return back()->with('success', 'Invitation resent.');
    }
}
