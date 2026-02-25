<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Office $office)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if user already reviewed this office
        $existingReview = Review::where('office_id', $office->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            $existingReview->update($data);
            return back()->with('success', 'Review updated successfully.');
        }

        $office->reviews()->create([
            ...$data,
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Review added successfully.');
    }

    public function delete(Request $request, Review $review)
    {
        $user = $request->user();
        abort_unless($user && ($user->id === $review->user_id || $user->isSuperAdmin()), 403);

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}

