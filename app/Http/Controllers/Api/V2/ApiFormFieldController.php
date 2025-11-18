<?php
// app/Http/Controllers/Api/V2/ApiFormFieldController.php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use App\Http\Requests\StoreFormFieldRequest;
use App\Http\Requests\UpdateFormFieldRequest;
use App\Http\Resources\FormFieldResource;

class ApiFormFieldController extends Controller
{
    /**
     * Add a new field to a form.
     *
     * @param  \App\Http\Requests\StoreFormFieldRequest  $request
     * @param  \App\Models\Form  $form
     * @return \App\Http\Resources\FormFieldResource
     */
    public function store(StoreFormFieldRequest $request, Form $form)
    {
        $field = $form->fields()->create($request->validated());
        return new FormFieldResource($field);
    }

    /**
     * Update an existing field.
     *
     * @param  \App\Http\Requests\UpdateFormFieldRequest  $request
     * @param  \App\Models\Form  $form
     * @param  \App\Models\FormField  $field
     * @return \App\Http\Resources\FormFieldResource
     */
    public function update(UpdateFormFieldRequest $request, Form $form, FormField $field)
    {
        if ($field->form_id !== $form->id) {
            abort(404);
        }

        $field->update($request->validated());
        return new FormFieldResource($field);
    }

    /**
     * Remove a field from a form.
     *
     * @param  \App\Models\Form  $form
     * @param  \App\Models\FormField  $field
     * @return \Illuminate\Http\Response
     */
    public function destroy(Form $form, FormField $field)
    {
        if ($field->form_id !== $form->id) {
            abort(404);
        }

        $field->delete();
        return response()->noContent();
    }
}
