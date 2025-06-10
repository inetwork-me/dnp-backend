<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ApiUserController extends Controller
{
    /**
     * Display a paginated list of users, optionally filtered by role.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $roleFilter = $request->get('role');

        $query = User::query();
        if ($roleFilter) {
            $query->role($roleFilter);
        }

        $users = $query->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Store a newly created user in storage with assigned roles.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'postal_code'   => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
            'country'       => 'nullable|string|max:100',
            'about_content' => 'nullable|string',
            'roles'         => 'required|array',
            'roles.*'       => 'string|exists:roles,name',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create(array_except($validated, ['roles']));
        $user->syncRoles($validated['roles']);

        return response()->json($user->load('roles'), 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return response()->json($user->load('roles'));
    }

    /**
     * Update the specified user in storage and sync roles.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'email'         => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'      => 'sometimes|nullable|string|min:8|confirmed',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'postal_code'   => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
            'country'       => 'nullable|string|max:100',
            'about_content' => 'nullable|string',
            'roles'         => 'sometimes|required|array',
            'roles.*'       => 'string|exists:roles,name',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update(array_except($validated, ['roles']));

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return response()->json($user->load('roles'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
