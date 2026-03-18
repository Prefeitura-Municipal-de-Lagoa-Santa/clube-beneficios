<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;
use LdapRecord\Container;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $username = $request->input('username');
            $password = $request->input('password');

            // 1) Try local authentication first
            $user = User::where('username', $username)
                ->orWhere('email', $username)
                ->first();

            if ($user && $user->password && Hash::check($password, $user->password)) {
                return $user;
            }

            // 2) Try LDAP authentication
            try {
                if (! class_exists(Container::class)) {
                    return null;
                }

                $connection = Container::getDefaultConnection();
                $connection->connect();
                $query = $connection->query();

                $ldapUser = $query->where('samaccountname', '=', $username)->first();

                if (! $ldapUser) {
                    $ldapUser = $query->where('mail', '=', $username)->first();
                }

                if (! $ldapUser) {
                    return null;
                }

                // Attempt LDAP bind with user's credentials
                if (! $connection->auth()->attempt($ldapUser['distinguishedname'][0], $password)) {
                    return null;
                }

                // Convert binary objectGUID to string
                $rawGuid = $ldapUser['objectguid'][0] ?? null;
                $guid = null;
                if ($rawGuid && strlen($rawGuid) === 16) {
                    $hex = bin2hex($rawGuid);
                    $guid = sprintf('%s-%s-%s-%s-%s',
                        implode('', array_reverse(str_split(substr($hex, 0, 8), 2))),
                        implode('', array_reverse(str_split(substr($hex, 8, 4), 2))),
                        implode('', array_reverse(str_split(substr($hex, 12, 4), 2))),
                        substr($hex, 16, 4),
                        substr($hex, 20, 12)
                    );
                }

                // Sync user to local database
                $user = User::updateOrCreate(
                    ['username' => $ldapUser['samaccountname'][0] ?? $username],
                    [
                        'name' => $ldapUser['cn'][0] ?? $ldapUser['displayname'][0] ?? $username,
                        'guid' => $guid,
                        'email' => $ldapUser['mail'][0] ?? null,
                        'domain' => $connection->getConfiguration()->get('base_dn'),
                        'matricula' => $ldapUser['description'][0] ?? null,
                    ]
                );

                return $user;
            } catch (\Exception $e) {
                report($e);
                return null;
            }
        });

        Fortify::loginView(function () {
            return Inertia::render('auth/Login');
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
