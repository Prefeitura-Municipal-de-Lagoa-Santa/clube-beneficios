<?php

namespace App\Http\Controllers;

use App\Models\MemberCard;
use Illuminate\Support\Facades\Cache;
use LdapRecord\Container;
use Inertia\Inertia;
use Inertia\Response;

class VerificationController extends Controller
{
    public function verify(string $token): Response
    {
        $card = MemberCard::with('user')->where('token', $token)->first();

        if (! $card) {
            return Inertia::render('Verification', [
                'found' => false,
                'active' => false,
                'name' => null,
            ]);
        }

        $user = $card->user;
        $isActive = $this->checkUserStatus($user);

        return Inertia::render('Verification', [
            'found' => true,
            'active' => $isActive,
            'name' => $user->name,
        ]);
    }

    private function checkUserStatus($user): bool
    {
        if (! $user->guid) {
            // Local user without LDAP — consider active
            return true;
        }

        $cacheKey = "verification_active_{$user->guid}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
            try {
                $connection = Container::getDefaultConnection();
                $connection->connect();
                $ldapUser = $connection->query()->where('samaccountname', '=', $user->username)->first();

                if (! $ldapUser) {
                    return false;
                }

                $uac = (int) ($ldapUser['useraccountcontrol'][0] ?? 0);

                return ($uac & 2) === 0;
            } catch (\Exception $e) {
                report($e);
                return true; // Fail-open if LDAP unreachable
            }
        });
    }
}
