<?php

use App\Http\Controllers\AccountRoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\OfficeInvitationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\SuperAdminController;
use App\Models\Office;
use App\Models\Booking;
use App\Models\OfficeInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Advertisement;

Route::get('/', function () {
    $today = now()->toDateString();
    $premiumAdverts = Advertisement::query()
        ->where('status', 'active')
        ->where('type', 'premium')
        ->whereDate('end_date', '>=', $today)
        ->with('office')
        ->latest()
        ->take(5)
        ->get()
        ->map(fn (Advertisement $ad) => [
            'id' => $ad->id,
            'type' => 'premium',
            'image_url' => $ad->image_url,
            'office_title' => $ad->office->title,
            'office_slug' => $ad->office->slug,
            'office_city' => $ad->office->city,
            'office_country' => $ad->office->country,
            'daily_rate' => $ad->office->daily_rate / 100,
        ]);

    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'premiumAdvertisements' => $premiumAdverts,
        'offices' => Office::query()
            ->where('is_active', true)
            ->withAvg('reviews', 'rating')
            ->with(['advertisements' => fn ($q) => $q
                ->where('status', 'active')
                ->whereDate('end_date', '>=', $today)
                ->orderByRaw("CASE WHEN type = 'premium' THEN 0 ELSE 1 END")
            ])
            ->orderByRaw(
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
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('created_at')
            ->take(6)
            ->get()
            ->transform(function (Office $office) {
                $avgRating = $office->reviews_avg_rating ?? 0;

                return [
                    'id' => $office->id,
                    'slug' => $office->slug,
                    'title' => $office->title,
                    'city' => $office->city,
                    'country' => $office->country,
                    'description' => $office->description,
                    'capacity' => $office->capacity,
                    'daily_rate' => $office->daily_rate / 100,
                    'image_urls' => $office->image_urls ?? [],
                    'workspace_type' => $office->workspace_type,
                    'average_rating' => round($avgRating, 1),
                    'advertisement_type' => $office->advertisements->first()?->type ?? null,
                ];
            }),
    ]);
})->name('home');

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-offices.xml', [SitemapController::class, 'offices'])->name('sitemap.offices');

Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');
Route::get('/privacy', fn () => Inertia::render('Privacy'))->name('privacy');
Route::get('/terms', fn () => Inertia::render('Terms'))->name('terms');
Route::get('/faq', fn () => Inertia::render('Faq'))->name('faq');

Route::get('/offices', [OfficeController::class, 'index'])->name('offices.index');
Route::get('/offices/{office}', [OfficeController::class, 'show'])->name('offices.show');
Route::post('/offices/{office}/reviews', [ReviewController::class, 'store'])->middleware('auth')->name('reviews.store');
Route::delete('/reviews/{review}', [ReviewController::class, 'delete'])->middleware('auth')->name('reviews.delete');

// Public invitation route
Route::get('/invitations/{token}', [OfficeInvitationController::class, 'showByToken'])->name('office-invitations.show-token');
Route::post('/invitations/register', [OfficeInvitationController::class, 'registerFromInvitation'])->name('office-invitations.register');

// Google OAuth routes
Route::get('/auth/{provider}', [SocialiteController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('auth.callback');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return Inertia::render('Dashboard', [
            'stats' => [
                'bookings' => Booking::where('user_id', $user->id)->count(),
                'listings' => Office::where('owner_id', $user->id)->count(),
            ],
            'recentBookings' => Booking::with('office')
                ->where('user_id', $user->id)
                ->latest()
                ->take(3)
                ->get()
                ->map(fn (Booking $booking) => [
                    'id' => $booking->id,
                    'office' => $booking->office?->title,
                    'start_date' => $booking->start_date?->toDateString(),
                    'end_date' => $booking->end_date?->toDateString(),
                    'status' => $booking->status,
                ]),
            'invitations' => OfficeInvitation::with(['office', 'inviter'])
                ->where('email', $user->email)
                ->whereNull('accepted_at')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (OfficeInvitation $invitation) => [
                    'id' => $invitation->id,
                    'office_title' => $invitation->office?->title,
                    'invited_by' => $invitation->inviter?->name ?? 'OfficeBnB',
                    'created_at' => $invitation->created_at?->toDateTimeString(),
                ]),
            'ownerCount' => User::where('role', 'owner')->count(),
        ]);
    })->name('dashboard');

    Route::post('/account/become-owner', [AccountRoleController::class, 'becomeOwner'])->name('account.become-owner');

    Route::get('/listings', [OfficeController::class, 'manage'])->name('listings.manage');
    Route::get('/listings/create', [OfficeController::class, 'create'])->name('listings.create');
    Route::post('/listings', [OfficeController::class, 'store'])->name('listings.store');
    Route::get('/listings/{office}/edit', [OfficeController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{office}', [OfficeController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{office}', [OfficeController::class, 'destroy'])->name('listings.destroy');

    Route::get('/offices/{office}/advertise', [AdvertisementController::class, 'create'])->name('advertisements.create');
    Route::post('/offices/{office}/advertisements', [AdvertisementController::class, 'store'])->name('advertisements.store');
    Route::get('/advertisements/{advertisement}/payment', [AdvertisementController::class, 'payment'])->name('advertisements.payment');
    Route::post('/advertisements/{advertisement}/pay', [AdvertisementController::class, 'processPayment'])->name('advertisements.process-payment');

    Route::post('/offices/{office}/invitations', [OfficeInvitationController::class, 'store'])->name('office-invitations.store');
    Route::post('/invitations/{invitation}/accept', [OfficeInvitationController::class, 'accept'])->name('office-invitations.accept');
    Route::post('/invitations/{invitation}/resend', [OfficeInvitationController::class, 'resend'])->name('office-invitations.resend');
    Route::delete('/invitations/{invitation}', [OfficeInvitationController::class, 'decline'])->name('office-invitations.decline');
    Route::get('/invitations', [OfficeInvitationController::class, 'index'])->name('office-invitations.index');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/manage', [BookingController::class, 'manage'])->name('bookings.manage');
    Route::post('/offices/{office}/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::patch('/bookings/{booking}', [BookingController::class, 'updateStatus'])->name('bookings.update-status');

    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/super-admin', [SuperAdminController::class, 'index'])->name('super-admin.dashboard');
    Route::get('/super-admin/landlords', [SuperAdminController::class, 'landlords'])->name('super-admin.landlords');
    Route::get('/super-admin/offices', [SuperAdminController::class, 'offices'])->name('super-admin.offices');
    Route::get('/super-admin/advertisements', [SuperAdminController::class, 'advertisements'])->name('super-admin.advertisements');
    Route::patch('/super-admin/advertisements/{advertisement}/approve', [SuperAdminController::class, 'approveAdvertisement'])->name('super-admin.advertisements.approve');
    Route::patch('/super-admin/advertisements/{advertisement}/refund', [SuperAdminController::class, 'refundAdvertisement'])->name('super-admin.advertisements.refund');
    Route::get('/super-admin/offices/create', [SuperAdminController::class, 'createOffice'])->name('super-admin.offices.create');
    Route::post('/super-admin/offices', [SuperAdminController::class, 'storeOffice'])->name('super-admin.offices.store');
    Route::patch('/super-admin/offices/{office}/toggle', [SuperAdminController::class, 'toggleOfficeStatus'])->name('super-admin.offices.toggle');
    Route::patch('/super-admin/users/{user}/role', [SuperAdminController::class, 'updateUserRole'])->name('super-admin.users.update-role');
    Route::delete('/super-admin/offices/{office}', [SuperAdminController::class, 'deleteOffice'])->name('super-admin.offices.delete');
});
