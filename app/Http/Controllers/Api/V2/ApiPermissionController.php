<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiPermissionController extends Controller
{
    /**
     * GET /api/v2/permissions
     * Get all permissions, optionally grouped by section
     */
    public function index(Request $request)
    {
        if ($request->has('grouped') && $request->grouped == 'true') {
            return response()->json(Permission::groupedBySection());
        }

        return response()->json(Permission::all());
    }

    /**
     * POST /api/v2/permissions
     * Create a new permission
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|unique:permissions,name',
            'guard_name' => 'nullable|string',
            'section'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permission = Permission::create([
            'name'       => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
            'section'    => $request->section,
        ]);

        return response()->json($permission, 201);
    }

    /**
     * GET /api/v2/permissions/{permission}
     */
    public function show(Permission $permission)
    {
        return response()->json($permission->load('roles'));
    }

    /**
     * PUT/PATCH /api/v2/permissions/{permission}
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|unique:permissions,name,' . $permission->id,
            'guard_name' => 'nullable|string',
            'section'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permission->update([
            'name'       => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
            'section'    => $request->section,
        ]);

        return response()->json($permission);
    }

    /**
     * DELETE /api/v2/permissions/{permission}
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(null, 204);
    }

    /**
     * POST /api/v2/permissions/bulk
     * Create multiple permissions at once
     */
    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*.name' => 'required|string|unique:permissions,name',
            'permissions.*.section' => 'required|string',
            'permissions.*.guard_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $created = [];
        foreach ($request->permissions as $permData) {
            $created[] = Permission::create([
                'name' => $permData['name'],
                'section' => $permData['section'],
                'guard_name' => $permData['guard_name'] ?? 'web',
            ]);
        }

        return response()->json($created, 201);
    }
}
