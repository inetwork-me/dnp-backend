<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CMS\FieldGroup;
use Illuminate\Http\Request;

class ACFController extends Controller
{
    protected $path;

    public function __construct()
    {
        $this->path = 'cms.field_groups.';
    }

    public function index()
    {
        $fieldGroups = FieldGroup::withCount('fields')->paginate(20);
        return view($this->path . 'index', compact('fieldGroups'));
    }

    public function create()
    {
        return view($this->path . 'create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location_rules' => 'nullable|array',
        ]);

        $data['location_rules'] = json_encode($request->location_rules ?? []);

        $group = FieldGroup::create($data);

        flash('Field group created successfully.')->success();
        return redirect()->route('cms.field-groups.index');
    }

    public function edit($id)
    {
        $group = FieldGroup::findOrFail($id);
        return view($this->path . 'edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        $group = FieldGroup::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location_rules' => 'nullable|array',
        ]);

        $data['location_rules'] = json_encode($request->location_rules ?? []);

        $group->update($data);

        flash('Field group updated successfully.')->success();
        return redirect()->route('cms.field-groups.index');
    }

    public function destroy($id)
    {
        $group = FieldGroup::findOrFail($id);
        $group->delete();

        flash('Field group deleted successfully.')->success();
        return redirect()->route('cms.field-groups.index');
    }
}
