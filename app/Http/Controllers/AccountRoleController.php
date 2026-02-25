<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountRoleController extends Controller
{
    public function becomeOwner(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'owner') {
            $user->update(['role' => 'owner']);
        }

        return back()->with('success', 'Your account is now an owner account.');
    }
}
