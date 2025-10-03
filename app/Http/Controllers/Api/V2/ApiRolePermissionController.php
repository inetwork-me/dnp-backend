<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiRolePermissionController extends Controller
{
    /**
     * POST /api/v2/roles/{role}/permissions
     * Assign permissions to a role
     */
    public function assignPermissionsToRole(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Permissions assigned successfully',
            'role' => $role->load('permissions'),
        ]);
    }

    /**
     * GET /api/v2/roles/{role}/permissions
     * Get all permissions for a role
     */
    public function getRolePermissions(Role $role)
    {
        // Exclude legacy permissions from the response
        $permissions = $role->permissions()->where('is_legacy', false)->get();
        return response()->json($permissions);
    }

    /**
     * DELETE /api/v2/roles/{role}/permissions/{permission}
     * Remove a specific permission from a role
     */
    public function removePermissionFromRole(Role $role, Permission $permission)
    {
        $role->revokePermissionTo($permission);

        return response()->json([
            'message' => 'Permission removed successfully',
        ]);
    }

    /**
     * POST /api/v2/users/{user}/roles
     * Assign roles to a user
     */
    public function assignRolesToUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->syncRoles($request->roles);

        return response()->json([
            'message' => 'Roles assigned successfully',
            'user' => $user->load('roles'),
        ]);
    }

    /**
     * GET /api/v2/users/{user}/roles
     * Get all roles for a user
     */
    public function getUserRoles(User $user)
    {
        return response()->json($user->roles);
    }

    /**
     * DELETE /api/v2/users/{user}/roles/{role}
     * Remove a role from a user
     */
    public function removeRoleFromUser(User $user, Role $role)
    {
        $user->removeRole($role);

        return response()->json([
            'message' => 'Role removed successfully',
        ]);
    }

    /**
     * POST /api/v2/users/{user}/permissions
     * Assign direct permissions to a user (bypass roles)
     */
    public function assignPermissionsToUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Permissions assigned successfully',
            'user' => $user->load('permissions'),
        ]);
    }

    /**
     * GET /api/v2/users/{user}/permissions
     * Get all permissions for a user (both from roles and direct)
     * Excludes legacy permissions from the response
     */
    public function getUserPermissions(User $user)
    {
        // Filter out legacy permissions
        $allPermissions = $user->getAllPermissions()->filter(function($perm) {
            return !$perm->is_legacy;
        })->values();

        $directPermissions = $user->permissions()->where('is_legacy', false)->get();

        $rolePermissions = $user->getPermissionsViaRoles()->filter(function($perm) {
            return !$perm->is_legacy;
        })->values();

        return response()->json([
            'all_permissions' => $allPermissions,
            'direct_permissions' => $directPermissions,
            'role_permissions' => $rolePermissions,
        ]);
    }
}
