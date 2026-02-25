<?php

namespace App\Http\Controllers;

use App\Mail\OfficeInvitationMail;
use App\Models\Advertisement;
use App\Models\Office;
use App\Models\OfficeInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => [
                'users' => User::count(),
                'owners' => User::whereIn('role', ['owner', 'admin'])->count(),
                'offices' => Office::count(),
                'active_offices' => Office::where('is_active', true)->count(),
            ],
        ]);
    }

    public function landlords(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $landlords = User::query()
            ->whereIn('role', ['owner', 'admin'])
            ->withCount('ownedOffices')
            ->latest()
            ->paginate(20)
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'offices_count' => $user->owned_offices_count,
                'created_at' => $user->created_at?->toDateString(),
            ]);

        return Inertia::render('SuperAdmin/Landlords', [
            'landlords' => $landlords,
        ]);
    }

    public function offices(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $offices = Office::query()
            ->with('owner')
            ->withCount('bookings')
            ->latest()
            ->paginate(20)
            ->through(fn (Office $office) => [
                'id' => $office->id,
                'slug' => $office->slug,
                'title' => $office->title,
                'city' => $office->city,
                'country' => $office->country,
                'owner' => $office->owner ? [
                    'id' => $office->owner->id,
                    'name' => $office->owner->name,
                    'email' => $office->owner->email,
                ] : null,
                'daily_rate' => $office->daily_rate / 100,
                'is_active' => $office->is_active,
                'bookings_count' => $office->bookings_count,
                'created_at' => $office->created_at?->toDateString(),
            ]);

        return Inertia::render('SuperAdmin/Offices', [
            'offices' => $offices,
        ]);
    }

    public function advertisements(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $status = $request->string('status')->trim()->toString();
        $status = $status ?: 'all';

        $advertisements = Advertisement::query()
            ->with(['office.owner', 'payment'])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Advertisement $advertisement) => [
                'id' => $advertisement->id,
                'type' => $advertisement->type,
                'status' => $advertisement->status,
                'image_url' => $advertisement->image_url,
                'start_date' => $advertisement->start_date?->toDateString(),
                'end_date' => $advertisement->end_date?->toDateString(),
                'amount' => $advertisement->amount_cents / 100,
                'payment_method' => $advertisement->payment_method,
                'office' => $advertisement->office ? [
                    'id' => $advertisement->office->id,
                    'slug' => $advertisement->office->slug,
                    'title' => $advertisement->office->title,
                    'city' => $advertisement->office->city,
                    'country' => $advertisement->office->country,
                ] : null,
                'owner' => $advertisement->office?->owner ? [
                    'id' => $advertisement->office->owner->id,
                    'name' => $advertisement->office->owner->name,
                    'email' => $advertisement->office->owner->email,
                ] : null,
                'payment' => $advertisement->payment ? [
                    'id' => $advertisement->payment->id,
                    'status' => $advertisement->payment->status,
                    'transaction_id' => $advertisement->payment->transaction_id,
                    'amount' => $advertisement->payment->amount_cents / 100,
                ] : null,
                'created_at' => $advertisement->created_at?->toDateString(),
            ]);

        return Inertia::render('SuperAdmin/Advertisements', [
            'filters' => [
                'status' => $status,
            ],
            'advertisements' => $advertisements,
        ]);
    }

    public function approveAdvertisement(Request $request, Advertisement $advertisement)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $advertisement->update([
            'status' => 'active',
        ]);

        return back()->with('success', 'Advertisement approved.');
    }

    public function refundAdvertisement(Request $request, Advertisement $advertisement)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        if ($advertisement->payment) {
            $advertisement->payment->update([
                'status' => 'refunded',
            ]);
        }

        $advertisement->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Advertisement refunded and cancelled.');
    }

    public function toggleOfficeStatus(Request $request, Office $office)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $office->update([
            'is_active' => !$office->is_active,
        ]);

        return back()->with('success', 'Office status updated.');
    }

    public function updateUserRole(Request $request, User $user)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'role' => ['required', 'in:renter,owner,admin'],
        ]);

        $user->update([
            'role' => $data['role'],
        ]);

        return back()->with('success', 'User role updated.');
    }

    public function deleteOffice(Request $request, Office $office)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $office->delete();

        return back()->with('success', 'Office deleted.');
    }

    public function createOffice(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return Inertia::render('SuperAdmin/CreateOffice', [
            'amenityOptions' => $this->amenityOptions(),
            'countryOptions' => $this->countryOptions(),
            'officeTypeOptions' => $this->officeTypeOptions(),
            'timezoneOptions' => $this->timezoneOptions(),
        ]);
    }

    public function storeOffice(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:80'],
            'country' => ['required', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1200'],
            'workspace_type' => ['nullable', 'string', 'max:60'],
            'size_sqft' => ['nullable', 'integer', 'min:100'],
            'meeting_rooms' => ['nullable', 'integer', 'min:0'],
            'desks' => ['nullable', 'integer', 'min:0'],
            'timezone' => ['nullable', 'string', 'max:60'],
            'capacity' => ['required', 'integer', 'min:1'],
            'daily_rate' => ['required', 'numeric', 'min:20'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:60'],
            'image_urls' => ['nullable', 'array'],
            'image_urls.*' => ['string', 'max:400'],
            'owner_email' => ['required', 'email'],
        ]);

        $ownerEmail = $data['owner_email'];
        unset($data['owner_email']);

        $owner = User::where('email', $ownerEmail)->first();

        if (!$owner) {
            // Create placeholder user
            $owner = User::create([
                'name' => 'Pending User',
                'email' => $ownerEmail,
                'password' => Str::random(32),
                'role' => 'owner',
            ]);
        } elseif (!$owner->isOwner()) {
            $owner->forceFill(['role' => 'owner'])->save();
        }

        $data['owner_id'] = $owner->id;
        $data['daily_rate'] = (int) round($data['daily_rate'] * 100);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['is_active'] = true;

        $office = Office::create($data);

        // Send invitation
        $invitation = OfficeInvitation::create([
            'office_id' => $office->id,
            'email' => $ownerEmail,
            'token' => Str::random(40),
            'invited_by' => $request->user()->id,
        ]);

        Mail::to($ownerEmail)->send(new OfficeInvitationMail($invitation));

        return redirect()->route('super-admin.offices')->with('success', 'Office created and invitation sent.');
    }

    private function uniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $suffix = 1;
        $candidate = $slug;

        while (Office::where('slug', $candidate)->exists()) {
            $suffix++;
            $candidate = $slug . '-' . $suffix;
        }

        return $candidate;
    }

    private function amenityOptions(): array
    {
        return config('office.amenities', []);
    }

    private function countryOptions(): array
    {
        return config('office.countries', []);
    }

    private function officeTypeOptions(): array
    {
        return config('office.types', []);
    }

    private function timezoneOptions(): array
    {
        return config('office.timezones', []);
    }
}
