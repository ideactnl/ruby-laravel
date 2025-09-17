<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::query()->with('roles');

        if (! auth()->user()?->hasRole('superadmin')) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Superadmin');
            });
        }

        if ($q = request('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $sort = in_array(request('sort'), ['name', 'email', 'created_at']) ? request('sort') : 'created_at';
        $dir = request('dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $dir);

        $perPage = (int) (request('per_page', 10));
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 10;
        $users = $query->paginate($perPage)->appends(request()->query());

        if (request()->wantsJson() || request()->ajax() || request('ajax')) {
            return response()->json([
                'data' => $users->getCollection()->map(function (User $u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'roles' => $u->roles->pluck('name')->values(),
                        'created_at' => optional($u->created_at)->toAtomString(),
                    ];
                }),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                ],
            ]);
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::whereNotIn('name', ['Superadmin'])->pluck('name', 'name');

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::whereNotIn('name', ['Superadmin'])->pluck('name', 'name');

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ? bcrypt($data['password']) : $user->password,
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('Superadmin')) {
            return back()->with('error', 'Cannot delete Superadmin.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
