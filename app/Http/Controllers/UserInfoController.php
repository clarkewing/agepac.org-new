<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserInfoController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($user->canSignInToApps(), 403);

        $app = array_search($user->token()?->oauth_client_id, config('passport.first_party_apps'), true);

        return response()->json([
            'sub' => (string) $user->getKey(),
            'given_name' => $user->first_name,
            'family_name' => $user->last_name,
            'role' => $app !== false && $user->hasPermission("{$app}:admin") ? 'admin' : 'member',
        ]);
    }
}
