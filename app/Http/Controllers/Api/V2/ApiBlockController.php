<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;

class ApiBlockController extends Controller
{
    /**
     * GET /api/blocks
     */
    public function index()
    {
        return Block::all();
    }

    /**
     * POST /api/blocks
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|unique:blocks,name',
            'label'      => 'required|array',
            'icon'       => 'nullable|string',
            'fields'     => 'nullable|array',
            'settings'   => 'nullable|array',
        ]);

        $block = Block::create($data);

        return response()->json($block, 201);
    }

    /**
     * GET /api/blocks/{block}
     */
    public function show(Block $block)
    {
        return $block;
    }

    /**
     * PUT /api/blocks/{block}
     */
    public function update(Request $request, Block $block)
    {
        $data = $request->validate([
            // allow same name on the current record
            'name'       => 'sometimes|string|unique:blocks,name,' . $block->id,
            'label'      => 'sometimes|array',
            'icon'       => 'nullable|string',
            'fields'     => 'nullable|array',
            'settings'   => 'nullable|array',
        ]);

        $block->update($data);

        return response()->json($block);
    }

    /**
     * DELETE /api/blocks/{block}
     */
    public function destroy(Block $block)
    {
        $block->delete();
        return response()->noContent();
    }
}
