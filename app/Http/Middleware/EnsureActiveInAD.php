<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LdapRecord\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveInAD
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->username || ! $user->guid) {
            return $next($request);
        }

        $cacheKey = "ldap_active_{$user->username}";
        $isActive = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            return $this->checkLdapStatus($user->username);
        });

        if (! $isActive) {
            Cache::forget($cacheKey);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Sua conta está inativa no Active Directory.');
        }

        return $next($request);
    }

    private function checkLdapStatus(string $guid): bool
    {
        try {
            $connection = Container::getDefaultConnection();
            $connection->connect();
            $ldapUser = $connection->query()->where('samaccountname', '=', $guid)->first();

            if (! $ldapUser) {
                return false;
            }

            $uac = (int) ($ldapUser['useraccountcontrol'][0] ?? 0);

            // Bit 2 (0x0002) = ACCOUNTDISABLE
            return ($uac & 2) === 0;
        } catch (\Exception $e) {
            report($e);
            // If LDAP is unreachable, allow access (fail-open to not lock everyone out)
            return true;
        }
    }
}
