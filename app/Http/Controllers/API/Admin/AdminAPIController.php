<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;

#[Group('Admin', 'Admin Management')]
#[Authenticated]
class AdminAPIController extends Controller
{
    public function getActivity(): Collection
    {
        return ActivityLog::query()->orderByDesc('created_at')->get();
    }

    public function getAdmins(): Collection
    {
        $users = User::all();

        return $users->filter(function ($user) {
            return $user->hasRole('Super Admin') || $user->hasRole('Admin');
        });
    }
}
