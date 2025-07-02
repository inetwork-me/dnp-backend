<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class ApiRolesController extends Controller
{
    /**
     * GET /api/v1/roles
     */
    public function index()
    {
        return response()->json(Role::all());
    }

    /**
     * POST /api/v1/roles
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|unique:roles,name',
            'guard_name' => 'nullable|string',
        ]);

        $role = Role::create($data);

        return response()->json($role, 201);
    }

    /**
     * GET /api/v1/roles/{role}
     */
    public function show(Role $role)
    {
        return response()->json($role);
    }

    /**
     * PUT/PATCH /api/v1/roles/{role}
     */
    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'       => 'required|string|unique:roles,name,' . $role->id,
            'guard_name' => 'nullable|string',
        ]);

        $role->update($data);

        return response()->json($role);
    }

    /**
     * DELETE /api/v1/roles/{role}
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(null, 204);
    }
}
