<?php
// app/Http/Controllers/Api/V2/ApiFormSubmissionController.php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormSubmissionRequest;
use App\Models\Form;
use App\Http\Resources\V2\FormSubmissionResource;
use Illuminate\Http\Response;

class ApiFormSubmissionController extends Controller
{
    /**
     * List submissions for a given form.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Form $form)
    {
        // eager-load nothing extra; return all submissions
        return FormSubmissionResource::collection(
            $form->submissions()->orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * Handle a public form submission by slug.
     *
     * @param  \App\Http\Requests\StoreFormSubmissionRequest  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    // public function submit(StoreFormSubmissionRequest $request, string $slug)
    // {
    //     $form = Form::where('slug', $slug)->firstOrFail();

    //     $submission = $form->submissions()->create([
    //         'data' => $request->validated()['data'],
    //     ]);

    //     return (new FormSubmissionResource($submission))
    //         ->response()
    //         ->setStatusCode(Response::HTTP_CREATED);
    // }

    public function submit(StoreFormSubmissionRequest $request, string $slug)
    {
        // 1) Load form + its fields by slug
        $form = Form::with('fields')->where('slug', $slug)->firstOrFail();

        // 2) Grab the raw submitted values
        $raw = $request->validated()['data'];  // e.g. ['email'=>'foo', 'message'=>'bar']

        // 3) Build a map of field definitions keyed by name
        $fieldsMap = $form->fields->keyBy('name');

        // 4) Enrich each entry with its label and value
        $payload = [];
        foreach ($raw as $name => $value) {
            $field = $fieldsMap->get($name);
            $payload[$name] = [
                'label' => $field ? $field->label : null, // { en: "...", ar: "..." }
                'value' => $value,
            ];
        }

        // 5) Store the enriched payload
        $submission = $form->submissions()->create([
            'data' => $payload,
        ]);

        return (new FormSubmissionResource($submission))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
