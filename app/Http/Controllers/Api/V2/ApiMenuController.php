<?php 
// app/Http/Controllers/Api/V2/ApiMenuController.php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class ApiMenuController extends Controller
{
    public function index()
    {
        return Menu::all();
    }

    public function show(Menu $menu)
    {
        return $menu;
    }

    public function store(Request $request)
    {
        if ($request->is_default) {
            Menu::where('is_default', true)->update(['is_default' => false]);
        }

        $menu = Menu::create($request->only(['name', 'slug', 'is_default', 'items']));

        return response()->json($menu, 201);
    }

    public function update(Request $request, Menu $menu)
    {
        if ($request->is_default) {
            Menu::where('is_default', true)->where('id', '!=', $menu->id)->update(['is_default' => false]);
        }

        $menu->update($request->only(['name', 'slug', 'is_default', 'items']));

        return response()->json($menu);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json(['success' => true]);
    }

    public function setDefault(Menu $menu)
    {
        Menu::where('is_default', true)->update(['is_default' => false]);
        $menu->update(['is_default' => true]);

        return response()->json(['success' => true]);
    }
}
