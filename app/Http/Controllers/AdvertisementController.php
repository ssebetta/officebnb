<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdvertisementController extends Controller
{
    public function create(Request $request, Office $office)
    {
        $this->ensureOfficeOwner($request, $office);

        return Inertia::render('Advertisements/Create', [
            'office' => [
                'id' => $office->id,
                'slug' => $office->slug,
                'title' => $office->title,
                'image_urls' => $office->image_urls ?? [],
            ],
        ]);
    }

    public function store(Request $request, Office $office)
    {
        $this->ensureOfficeOwner($request, $office);

        $data = $request->validate([
            'type' => ['required', 'in:basic,premium'],
            'image_url' => ['required_without:image_file', 'string', 'max:500'],
            'image_file' => ['required_without:image_url', 'image', 'max:5120'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'payment_method' => ['required', 'in:stripe,mobile_money'],
        ]);

        // Calculate amount based on type and duration
        $startDate = new \DateTime($data['start_date']);
        $endDate = new \DateTime($data['end_date']);
        $days = $endDate->diff($startDate)->days + 1;

        $dailyRate = $data['type'] === 'premium' ? 999 : 399; // in cents ($9.99 or $3.99 per day)
        $amountCents = $dailyRate * $days;

        $imageUrl = $data['image_url'] ?? null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('office-ads', 'public');
            $imageUrl = Storage::url($path);
        }

        $advertisement = Advertisement::create([
            'office_id' => $office->id,
            'type' => $data['type'],
            'image_url' => $imageUrl,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'pending_payment',
            'amount_cents' => $amountCents,
            'payment_method' => $data['payment_method'],
        ]);

        return redirect()->route('advertisements.payment', $advertisement)->with('success', 'Advertisement created. Proceed to payment.');
    }

    public function payment(Request $request, Advertisement $advertisement)
    {
        $this->ensureOfficeOwner($request, $advertisement->office);

        return Inertia::render('Advertisements/Payment', [
            'advertisement' => [
                'id' => $advertisement->id,
                'type' => $advertisement->type,
                'start_date' => $advertisement->start_date->format('Y-m-d'),
                'end_date' => $advertisement->end_date->format('Y-m-d'),
                'amount_cents' => $advertisement->amount_cents,
                'amount_display' => number_format($advertisement->amount_cents / 100, 2),
                'payment_method' => $advertisement->payment_method,
                'image_url' => $advertisement->image_url,
            ],
            'stripe_public_key' => config('cashier.key'),
        ]);
    }

    public function processPayment(Request $request, Advertisement $advertisement)
    {
        $this->ensureOfficeOwner($request, $advertisement->office);

        $request->validate([
            'payment_method' => ['required', 'in:stripe,mobile_money'],
            'stripe_reference' => ['nullable', 'string', 'max:120'],
        ]);

        try {
            $transactionId = $request->payment_method === 'stripe'
                ? ($request->stripe_reference ?: 'STRIPE_' . uniqid())
                : 'MM_' . uniqid();

            // Create payment record
            $payment = $advertisement->payment()->create([
                'payment_gateway' => $advertisement->payment_method,
                'transaction_id' => $transactionId,
                'amount_cents' => $advertisement->amount_cents,
                'status' => $request->payment_method === 'stripe' ? 'completed' : 'pending',
                'payment_details' => ['method' => $advertisement->payment_method],
            ]);

            // Update advertisement status: active if Stripe payment completed, else pending review
            $adStatus = $payment->status === 'completed' ? 'active' : 'pending_payment';
            $advertisement->update(['status' => $adStatus]);

            return redirect()->route('listings.manage')->with('success', 'Advertisement activated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Payment failed: ' . $e->getMessage()]);
        }
    }

    private function ensureOfficeOwner(Request $request, Office $office): void
    {
        if (
            $request->user()->id !== $office->owner_id &&
            !$office->managers->contains($request->user()) &&
            !$request->user()->isSuperAdmin()
        ) {
            abort(403, 'Unauthorized');
        }
    }
}

