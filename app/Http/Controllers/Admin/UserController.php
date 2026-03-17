<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use LdapRecord\Container;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::with('roles')
            ->when($request->input('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('username', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'roles' => Role::all(),
            'filters' => $request->only('search'),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return back()->with('success', 'Permissões atualizadas com sucesso!');
    }

    public function ldapSearch(Request $request): Response
    {
        $request->validate(['query' => 'required|string|min:2']);

        $results = [];

        try {
            $connection = Container::getDefaultConnection();
            $connection->connect();
            $query = $connection->query()
                ->whereContains('cn', $request->input('query'))
                ->orWhereContains('samaccountname', $request->input('query'))
                ->limit(20);

            $ldapUsers = $query->get();

            foreach ($ldapUsers as $ldapUser) {
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
                $sam = $ldapUser['samaccountname'][0] ?? '';
                $results[] = [
                    'guid' => $guid,
                    'name' => $ldapUser['cn'][0] ?? $ldapUser['displayname'][0] ?? '',
                    'username' => $sam,
                    'email' => $ldapUser['mail'][0] ?? '',
                    'matricula' => $ldapUser['employeeid'][0] ?? $ldapUser['employeenumber'][0] ?? '',
                    'already_imported' => User::where('username', $sam)->exists(),
                ];
            }
        } catch (\Exception $e) {
            report($e);
        }

        return Inertia::render('admin/users/Index', [
            'users' => User::with('roles')->orderBy('name')->paginate(15),
            'roles' => Role::all(),
            'filters' => $request->only('search'),
            'ldapResults' => $results,
            'ldapQuery' => $request->input('query'),
        ]);
    }

    public function ldapImport(Request $request): RedirectResponse
    {
        $request->validate([
            'guid' => 'required|string',
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'nullable|email',
            'matricula' => 'nullable|string',
        ]);

        User::updateOrCreate(
            ['username' => $request->input('username')],
            [
                'name' => $request->input('name'),
                'guid' => $request->input('guid'),
                'email' => $request->input('email'),
                'matricula' => $request->input('matricula'),
                'domain' => config('ldap.connections.default.base_dn'),
            ]
        );

        return back()->with('success', 'Usuário importado com sucesso!');
    }
}
