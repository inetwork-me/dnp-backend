<?php
// app/Http/Controllers/Api/V2/ApiFormSubmissionController.php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormSubmissionRequest;
use App\Models\Form;
use App\Http\Resources\V2\FormSubmissionResource;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ApiFormSubmissionController extends Controller
{
    /**
     * List submissions for a given form.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Form $form)
    {
        $perPage = $request->input('per_page', 10);

        $query = $form->submissions()->orderBy('created_at', 'desc');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        return FormSubmissionResource::collection(
            $query->paginate($perPage)
        );
    }

    /**
     * Export submissions for a given form as JSON (for client-side Excel generation).
     */
    public function export(Request $request, Form $form)
    {
        $query = $form->submissions()->orderBy('created_at', 'desc');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        return FormSubmissionResource::collection($query->get());
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

    /**
     * Delete a form submission.
     *
     * @param  \App\Models\Form  $form
     * @param  int  $submissionId
     * @return \Illuminate\Http\Response
     */
    public function destroy(Form $form, int $submissionId)
    {
        $submission = $form->submissions()->findOrFail($submissionId);
        $submission->delete();

        return response()->json([
            'message' => 'Submission deleted successfully'
        ], Response::HTTP_OK);
    }
}
