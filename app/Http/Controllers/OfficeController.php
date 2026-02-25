<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();
        $location = $request->string('location')->trim()->toString();
        $teamSize = $request->integer('team_size');
        $sort = $request->string('sort')->trim()->toString();
        if ($sort === '') {
            $sort = 'recommended';
        }
        $minRate = $request->integer('min_rate');
        $maxRate = $request->integer('max_rate');
        $capacity = $request->integer('capacity');
        $today = now()->toDateString();

        $officesQuery = Office::query()
            ->where('is_active', true)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($location, function ($query) use ($location) {
                $query->where(function ($inner) use ($location) {
                    $inner->where('city', 'like', "%{$location}%")
                        ->orWhere('country', 'like', "%{$location}%");
                });
            })
            ->when($teamSize, fn ($query) => $query->where('capacity', '>=', $teamSize))
            ->when($minRate, fn ($query) => $query->where('daily_rate', '>=', $minRate * 100))
            ->when($maxRate, fn ($query) => $query->where('daily_rate', '<=', $maxRate * 100))
            ->when($capacity, fn ($query) => $query->where('capacity', '>=', $capacity))
            ->withAvg('reviews', 'rating')
            ->with(['advertisements' => fn ($q) => $q
                ->where('status', 'active')
                ->whereDate('end_date', '>=', now())
                ->orderByRaw("CASE WHEN type = 'premium' THEN 0 ELSE 1 END")
            ])
            ->when($sort === 'premium', function ($query) use ($today) {
                $query->whereHas('advertisements', function ($adverts) use ($today) {
                    $adverts->where('status', 'active')
                        ->where('type', 'premium')
                        ->whereDate('end_date', '>=', $today);
                });
            })
            ->when($sort === 'basic', function ($query) use ($today) {
                $query->whereHas('advertisements', function ($adverts) use ($today) {
                    $adverts->where('status', 'active')
                        ->where('type', 'basic')
                        ->whereDate('end_date', '>=', $today);
                });
            });

        if ($sort === 'best_reviewed') {
            $officesQuery->orderByDesc('reviews_avg_rating');
        } else {
            $officesQuery->orderByRaw(
                "CASE
                    WHEN EXISTS (
                        SELECT 1 FROM advertisements
                        WHERE advertisements.office_id = offices.id
                        AND advertisements.type = 'premium'
                        AND advertisements.status = 'active'
                        AND advertisements.end_date >= ?
                    ) THEN 0
                    WHEN EXISTS (
                        SELECT 1 FROM advertisements
                        WHERE advertisements.office_id = offices.id
                        AND advertisements.type = 'basic'
                        AND advertisements.status = 'active'
                        AND advertisements.end_date >= ?
                    ) THEN 1
                    ELSE 2
                END",
                [$today, $today]
            )
            ->orderByDesc('reviews_avg_rating');
        }

        $offices = $officesQuery
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString()
            ->through(function (Office $office) {
                $avgRating = $office->reviews_avg_rating ?? 0;

                return [
                    'id' => $office->id,
                    'slug' => $office->slug,
                    'title' => $office->title,
                    'city' => $office->city,
                    'country' => $office->country,
                    'capacity' => $office->capacity,
                    'daily_rate' => $office->daily_rate / 100,
                    'image_urls' => $office->image_urls ?? [],
                    'workspace_type' => $office->workspace_type,
                    'average_rating' => round($avgRating, 1),
                    'advertisement_type' => $office->advertisements->first()?->type ?? null,
                ];
            });

        return Inertia::render('Offices/Index', [
            'filters' => [
                'search' => $search,
                'location' => $location,
                'team_size' => $teamSize,
                'sort' => $sort,
                'min_rate' => $minRate,
                'max_rate' => $maxRate,
                'capacity' => $capacity,
            ],
            'offices' => $offices,
        ]);
    }

    public function show(Request $request, Office $office)
    {
        $user = $request->user();
        $isManager = $user && $office->managers()->where('users.id', $user->id)->exists();

        if (! $office->is_active && ! $user?->isSuperAdmin() && $office->owner_id !== $user?->id && ! $isManager) {
            abort(404);
        }

        return Inertia::render('Offices/Show', [
            'office' => [
                'id' => $office->id,
                'slug' => $office->slug,
                'title' => $office->title,
                'city' => $office->city,
                'country' => $office->country,
                'address' => $office->address,
                'description' => $office->description,
                'amenities' => $office->amenities ?? [],
                'image_urls' => $office->image_urls ?? [],
                'workspace_type' => $office->workspace_type,
                'size_sqft' => $office->size_sqft,
                'meeting_rooms' => $office->meeting_rooms,
                'desks' => $office->desks,
                'timezone' => $office->timezone,
                'capacity' => $office->capacity,
                'daily_rate' => $office->daily_rate / 100,
                'owner' => $office->owner ? [
                    'id' => $office->owner->id,
                    'name' => $office->owner->name,
                ] : null,
                'reviews' => $office->reviews()
                    ->with('user:id,name')
                    ->latest()
                    ->get()
                    ->map(fn($review) => [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at->diffForHumans(),
                        'user' => [
                            'id' => $review->user->id,
                            'name' => $review->user->name,
                        ],
                    ]),
                'averageRating' => $office->reviews()->avg('rating') ?? 0,
                'reviewCount' => $office->reviews()->count(),
            ],
        ]);
    }

    public function create(Request $request)
    {
        $this->ensureOwner($request);

        return Inertia::render('Offices/Create', [
            'amenityOptions' => $this->amenityOptions(),
            'countryOptions' => $this->countryOptions(),
            'officeTypeOptions' => $this->officeTypeOptions(),
            'timezoneOptions' => $this->timezoneOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureOwner($request);

        $data = $this->validateOffice($request);
        $data['owner_id'] = $request->user()->id;
        $data['daily_rate'] = (int) round($data['daily_rate'] * 100);
        $data['slug'] = $this->uniqueSlug($data['title']);

        // Handle image uploads and URLs
        $data['image_urls'] = $this->processImages($request);
        unset($data['image_files']);

        $office = Office::create($data);

        return redirect()->route('offices.show', $office)->with('success', 'Office listing created.');
    }

    public function edit(Request $request, Office $office)
    {
        $this->ensureOfficeOwner($request, $office);

        return Inertia::render('Offices/Edit', [
            'office' => $this->serializeOfficeForForm($office),
            'amenityOptions' => $this->amenityOptions(),
            'countryOptions' => $this->countryOptions(),
            'officeTypeOptions' => $this->officeTypeOptions(),
            'timezoneOptions' => $this->timezoneOptions(),
        ]);
    }

    public function update(Request $request, Office $office)
    {
        $this->ensureOfficeOwner($request, $office);

        $data = $this->validateOffice($request);
        $data['daily_rate'] = (int) round($data['daily_rate'] * 100);

        // Handle image uploads and URLs
        $data['image_urls'] = $this->processImages($request, $office);
        unset($data['image_files']);

        $office->update($data);

        return redirect()->route('offices.show', $office)->with('success', 'Office listing updated.');
    }

    public function destroy(Request $request, Office $office)
    {
        $this->ensureOfficeOwner($request, $office);

        $office->delete();

        return redirect()->route('listings.manage')->with('success', 'Office listing removed.');
    }

    public function manage(Request $request)
    {
        $this->ensureOwner($request);

        $user = $request->user();

        $offices = Office::query()
            ->select('offices.*')
            ->where('owner_id', $user->id)
            ->orWhereHas('managers', fn ($query) => $query->where('users.id', $user->id))
            ->withCount('bookings')
            ->with(['managers:id,name,email', 'invitations' => fn ($query) => $query->whereNull('accepted_at')->with('inviter:id,name')])
            ->latest()
            ->distinct()
            ->get()
            ->map(fn (Office $office) => [
                'id' => $office->id,
                'slug' => $office->slug,
                'title' => $office->title,
                'city' => $office->city,
                'country' => $office->country,
                'daily_rate' => $office->daily_rate / 100,
                'is_active' => $office->is_active,
                'bookings_count' => $office->bookings_count,
                'managers' => $office->managers->map(fn ($manager) => [
                    'id' => $manager->id,
                    'name' => $manager->name,
                    'email' => $manager->email,
                ]),
                'pending_invitations' => $office->invitations->map(fn ($invitation) => [
                    'id' => $invitation->id,
                    'email' => $invitation->email,
                    'invited_by' => $invitation->inviter?->name ?? 'You',
                    'created_at' => $invitation->created_at?->diffForHumans(),
                ]),
            ]);

        return Inertia::render('Offices/Manage', [
            'offices' => $offices,
        ]);
    }

    private function validateOffice(Request $request): array
    {
        return $request->validate([
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
            'image_urls' => ['nullable', 'array', 'max:5'],
            'image_urls.*' => ['string', 'max:400'],
            'image_files' => ['nullable', 'array', 'max:5'],
            'image_files.*' => ['file', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);
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

    private function processImages(Request $request, ?Office $office = null): array
    {
        $imageUrls = [];

        // Add URLs from text inputs
        if ($request->has('image_urls')) {
            $imageUrls = array_filter($request->input('image_urls', []), fn($url) => !empty(trim($url)));
        }

        // Handle file uploads
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('office-images', 'public');
                    $imageUrls[] = '/storage/' . $path;
                }
            }
        }

        // Limit to 5 images total
        return array_values(array_slice($imageUrls, 0, 5));
    }

    private function serializeOfficeForForm(Office $office): array
    {
        return [
            'id' => $office->id,
            'slug' => $office->slug,
            'title' => $office->title,
            'city' => $office->city,
            'country' => $office->country,
            'address' => $office->address,
            'description' => $office->description,
            'amenities' => $office->amenities ?? [],
            'image_urls' => $office->image_urls ?? [],
            'workspace_type' => $office->workspace_type,
            'size_sqft' => $office->size_sqft,
            'meeting_rooms' => $office->meeting_rooms,
            'desks' => $office->desks,
            'timezone' => $office->timezone,
            'capacity' => $office->capacity,
            'daily_rate' => $office->daily_rate / 100,
            'is_active' => $office->is_active,
        ];
    }

    private function ensureOwner(Request $request): void
    {
        $user = $request->user();
        abort_unless($user, 403);

        if (! $user->isOwner()) {
            $user->forceFill(['role' => 'owner'])->save();
        }
    }

    private function ensureOfficeOwner(Request $request, Office $office): void
    {
        $user = $request->user();

        $isManager = $user && $office->managers()->where('users.id', $user->id)->exists();

        abort_unless($user?->isSuperAdmin() || $office->owner_id === $user?->id || $isManager, 403);
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
