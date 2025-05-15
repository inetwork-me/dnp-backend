<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CMS\CustomField;
use App\Models\CMS\FieldGroup;
use App\Models\CMS\FieldGroupLocationRule;
use Illuminate\Http\Request;

class ACFController extends Controller
{
    protected $path;

    public function __construct()
    {
        $this->path = 'cms.acf.';
    }

    public function index()
    {
        $fieldGroups = FieldGroup::withCount('customFields')->paginate(20);
        return view($this->path . 'index', compact('fieldGroups'));
    }

    public function create()
    {
        return view($this->path . 'create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',

            'fields' => 'required|array',
            'fields.*.type' => 'required|string',
            'fields.*.label' => 'required|string',
            'fields.*.name' => 'required|string',
            'fields.*.choices' => 'nullable',
            'fields.*.default' => 'nullable',

            'fields.*.foo' => 'prohibited',
            'fields.*.bar' => 'prohibited',

            'rules' => 'required|array',
            'rules.*.object' => 'required|string',
            'rules.*.operator' => 'required|string',
            'rules.*.value' => 'required|string',

            'rules.*.foo' => 'prohibited',
            'rules.*.bar' => 'prohibited',
        ]);

        // dd($request->all());

        $field_group = FieldGroup::create([
            'title' => $request->name,
            'description' => $request->description,
            'slug' => \Str::slug($request->name) . '-' . uniqid(),
            'key' => 'group_' . uniqid(),
        ]);

        if ($request->has('fields')) {
            foreach ($request->fields as $field) {
                $choicesArray = [];
                if (isset($field['choices']) && $field['choices'] != null) {
                    $pairs = explode(',', $field['choices']);

                    foreach ($pairs as $pair) {
                        if (strpos($pair, '=>') !== false) {
                            list($key, $value) = explode('=>', $pair, 2);
                            $choicesArray[trim($key)] = trim($value);
                        }
                    }
                }

                CustomField::create([
                    'field_group_id' => $field_group->id,
                    "label" => $field['label'],
                    "name" => $field['name'],
                    "type" => $field['type'],
                    "options" => $choicesArray,
                    "is_required" => isset($field['validation']) && $field['validation'] == "1" ? true : false,
                    "order" => 0,
                    "choices" => $field['choices'],
                    "select_multiple" => isset($field['multiple']) && $field['multiple'] == "1" ? true : false,
                    "default_values" => $field['default'],
                ]);
            }
        }


        if ($request->has('rules')) {
            foreach ($request->rules as $rule) {
                FieldGroupLocationRule::create([
                    'field_group_id' => $field_group->id,
                    'key' => $rule['object'],
                    'operator' => $rule['operator'],
                    'value' => $rule['value'],
                ]);
            }
        }

        flash('Field group created successfully.')->success();
        return redirect()->route('cms.acf.index');
    }

    public function edit($id)
    {
        $fieldGroup = FieldGroup::findOrFail($id);
        return view($this->path . 'edit', compact('fieldGroup'));
    }

    public function fields($id)
    {
        $fieldGroup = FieldGroup::findOrFail($id);
        return view($this->path . 'fields', compact('fieldGroup'));
    }

    public function editField($id)
    {
        $field = CustomField::findOrFail($id);
        return view($this->path . 'edit-field', compact('field'));
    }

    public function updateField(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
            'label' => 'required|string',
            'name' => 'required|string',
            'choices' => 'nullable',
            'default' => 'nullable',
        ]);
        

        // Find the existing field group or fail
        $choicesArray = [];
        if (isset($request->choices) && $request->choices != null) {
            $pairs = explode(',', $request->choices);

            foreach ($pairs as $pair) {
                if (strpos($pair, '=>') !== false) {
                    list($key, $value) = explode('=>', $pair, 2);
                    $choicesArray[trim($key)] = trim($value);
                }
            }
        }

        $field = CustomField::findOrFail($id);
        $field->label = $request->label;
        $field->name = $request->name;
        $field->type = $request->type;
        $field->options = $choicesArray;
        $field->is_required = isset($request->validation) && $request->validation == "1" ? true : false;
        $field->order = 0;
        $field->choices = $request->choices;
        $field->select_multiple = isset($request->multiple) && $request->multiple == "1" ? true : false;
        $field->default_values = $request->default;

        $field->save();

        flash('Field updated successfully.')->success();
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',

            'fields' => 'required|array',
            'fields.*.type' => 'required|string',
            'fields.*.label' => 'required|string',
            'fields.*.name' => 'required|string',
            'fields.*.choices' => 'nullable',
            'fields.*.default' => 'nullable',

            'fields.*.foo' => 'prohibited',
            'fields.*.bar' => 'prohibited',

            'rules' => 'required|array',
            'rules.*.object' => 'required|string',
            'rules.*.operator' => 'required|string',
            'rules.*.value' => 'required|string',

            'rules.*.foo' => 'prohibited',
            'rules.*.bar' => 'prohibited',
        ]);

        // Find the existing field group or fail
        $field_group = FieldGroup::findOrFail($id);

        // Update field group properties
        $field_group->update([
            'title' => $request->name,
            'description' => $request->description,
        ]);

        // Delete existing related fields and rules
        CustomField::where('field_group_id', $field_group->id)->delete();
        FieldGroupLocationRule::where('field_group_id', $field_group->id)->delete();

        // Recreate custom fields
        if ($request->has('fields')) {
            foreach ($request->fields as $field) {
                $choicesArray = [];
                if (isset($field['choices']) && $field['choices'] != null) {
                    $pairs = explode(',', $field['choices']);

                    foreach ($pairs as $pair) {
                        if (strpos($pair, '=>') !== false) {
                            list($key, $value) = explode('=>', $pair, 2);
                            $choicesArray[trim($key)] = trim($value);
                        }
                    }
                }

                CustomField::create([
                    'field_group_id' => $field_group->id,
                    "label" => $field['label'],
                    "name" => $field['name'],
                    "type" => $field['type'],
                    "options" => $choicesArray,
                    "is_required" => isset($field['validation']) && $field['validation'] == "1" ? true : false,
                    "order" => 0,
                    "choices" => $field['choices'],
                    "select_multiple" => isset($field['multiple']) && $field['multiple'] == "1" ? true : false,
                    "default_values" => $field['default'],
                ]);
            }
        }

        // Recreate rules
        if ($request->has('rules')) {
            foreach ($request->rules as $rule) {
                FieldGroupLocationRule::create([
                    'field_group_id' => $field_group->id,
                    'key' => $rule['object'],
                    'operator' => $rule['operator'],
                    'value' => $rule['value'],
                ]);
            }
        }

        flash('Field group updated successfully.')->success();
        return redirect()->route('cms.acf.index');
    }


    public function destroy($id)
    {
        $group = FieldGroup::findOrFail($id);
        $group->delete();

        flash('Field group deleted successfully.')->success();
        return redirect()->route('cms.acf.index');
    }
}
