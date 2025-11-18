<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\LanguageCollection;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiLanguagesController extends Controller
{
    /**
     * GET /api/v2/languages
     * Return a paginated list of all languages.
     */
    public function index(Request $request)
    {
        $perPage   = $request->query('per_page', 100);
        $languages = Language::orderBy('created_at', 'desc')->paginate($perPage);

        return new LanguageCollection($languages);
    }

    /**
     * POST /api/v2/languages
     * Validate and create a new language.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code'           => ['required', 'string', 'unique:languages,code'],
            'name'           => ['required', 'string', 'unique:languages,name'],
            'app_lang_code'  => ['required', 'string', 'unique:languages,app_lang_code'],
            'rtl'            => ['sometimes', 'boolean'],
            'status'         => ['sometimes', 'boolean'],
            'is_default'     => ['sometimes', 'boolean'],
        ]);

        $language = Language::create($data);

        return response()->json([
            'success'  => true,
            'language' => [
                'id'              => $language->id,
                'name'            => $language->name,
                'code'            => $language->code,
                'app_lang_code'   => $language->app_language_code,
                'rtl'             => (bool) $language->rtl,
                'status'             => (bool) $language->status,
                'is_default'      => (bool) $language->is_default,
            ],
        ], 201);
    }

    /**
     * GET /api/v2/languages/{lang}
     * Return a single language by ID.
     */
    public function show(Language $language)
    {
        return response()->json([
            'success'  => true,
            'language' => [
                'id'              => $language->id,
                'name'            => $language->name,
                'code'            => $language->code,
                'app_lang_code'   => $language->app_language_code,
                'rtl'             => (bool) $language->rtl,
                'status'             => (bool) $language->status,
                'is_default'      => (bool) $language->is_default,
            ],
        ], 200);
    }

    /**
     * PUT/PATCH /api/v2/languages/{lang}
     * Validate and update an existing language.
     */
    public function update(Request $request, Language $lang)
    {
        // Use Rule::unique with ignore($lang->id) so we can update without tripping the unique constraint on itself.
        $data = $request->validate([
            'code'           => [
                'required',
                'string',
                Rule::unique('languages', 'code')->ignore($lang->id),
            ],
            'name'           => [
                'required',
                'string',
                Rule::unique('languages', 'name')->ignore($lang->id),
            ],
            'app_lang_code'  => [
                'required',
                'string',
                Rule::unique('languages', 'app_lang_code')->ignore($lang->id),
            ],
            'rtl'            => ['sometimes', 'boolean'],
            'status'         => ['sometimes', 'boolean'],
            'is_default'     => ['sometimes', 'boolean'],
        ]);

        $lang->update($data);

        return response()->json([
            'success'  => true,
            'language' => [
                'id'              => $lang->id,
                'name'            => $lang->name,
                'code'            => $lang->code,
                'app_lang_code'   => $lang->app_lang_code,
                'rtl'             => (bool) $lang->rtl,
                'status'             => (bool) $lang->status,
                'is_default'      => (bool) $lang->is_default,
            ],
        ], 200);
    }

    /**
     * DELETE /api/v2/languages/{lang}
     * Delete a language permanently.
     */
    public function destroy(Language $language)
    {

        $language->delete();

        return response()->json([
            'success' => true,
            'message' => 'Language deleted successfully.',
        ], 200);
    }
}
