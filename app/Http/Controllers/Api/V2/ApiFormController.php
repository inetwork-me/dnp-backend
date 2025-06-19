<?php

// app/Http/Controllers/Api/FormController.php
namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Http\Resources\V2\FormResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ApiFormController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);

        // paginate *before* retrieving the results
        $forms = Form::with('fields')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // wrap the LengthAwarePaginator in your resource
        return FormResource::collection($forms);
    }

    public function store(StoreFormRequest $request)
    {
        $form = Form::create($request->validated());
        return new FormResource($form->load('fields'));
    }

    public function show(Form $form)
    {
        return new FormResource($form->load('fields'));
    }

    public function showBySlug(string $slug)
    {
        $form = Form::with('fields')
            ->where('slug', $slug)
            ->firstOrFail();

        return new FormResource($form);
    }

    // app/Http/Controllers/Api/FormController.php



    public function update(UpdateFormRequest $request, Form $form)
    {
        // 1. Update form metadata
        $form->update($request->only(['label', 'slug', 'settings']));

        // 2. If the client passed a `fields` array, sync them
        if ($request->has('fields')) {
            DB::transaction(function () use ($request, $form) {
                $incoming = collect($request->input('fields'));

                // a) IDs of fields we should keep
                $keepIds = $incoming->pluck('id')->filter()->all();

                // b) Delete any fields that were removed on the client
                $form->fields()
                    ->whereNotIn('id', $keepIds)
                    ->delete();

                // c) Loop through incoming fields
                foreach ($incoming as $fieldData) {
                    // remove id so create doesn’t choke on it
                    $data = Arr::except($fieldData, ['id']);

                    if (!empty($fieldData['id'])) {
                        // update existing
                        $form->fields()
                            ->findOrFail($fieldData['id'])
                            ->update($data);
                    } else {
                        // create new
                        $form->fields()
                            ->create($data);
                    }
                }
            });
        }

        return new FormResource($form->load('fields'));
    }


    // public function update(UpdateFormRequest $request, Form $form)
    // {
    //     $form->update($request->validated());
    //     return new FormResource($form->load('fields'));
    // }

    public function destroy(Form $form)
    {
        $form->delete();
        return response()->noContent();
    }
}
