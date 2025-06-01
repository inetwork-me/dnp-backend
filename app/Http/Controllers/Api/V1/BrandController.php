<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BrandCollection;
use App\Models\Brand;
use App\Models\BrandTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Utility\SearchUtility;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brand_query = Brand::query();
        if (!empty($request->name)) {
            $brand_query->where('name', 'like', '%' . $request->name . '%');
            SearchUtility::store($request->name);
        }
        $perPage = $request->input('per_page', 10);
        return new BrandCollection($brand_query->paginate($perPage));
    }

    /**
     * Store a newly created Brand via JSON payload.
     *
     * Expected JSON payload:
     * {
     *   "logo": "data:image/png;base64,….",          // required
     *   "translations": {
     *     "en": {
     *       "name": "English Brand Name",             // required
     *       "meta_title": "Meta Title in English",    // optional
     *       "meta_description": "…",                  // optional
     *     },
     *     "ar": {                                     // optional entirely
     *       "name": "اسم الماركة",                     // required if other AR fields present
     *       "meta_title": "عنوان الميتا بالعربية",      // optional
     *       "meta_description": "…",                  // optional
     *     }
     *   }
     * }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1) Grab all incoming JSON
        $payload = $request->all();

        // 2) Define validation rules
        $validator = Validator::make($payload, [
            'logo'                           => ['required', 'string'],
            'translations.en.name'           => ['required', 'string', 'max:255'],
            'translations.en.meta_title'     => ['nullable', 'string', 'max:100'],
            'translations.en.meta_description' => ['nullable', 'string', 'max:500'],

            // Arabic fields are nullable by default
            'translations.ar.name'           => ['nullable', 'string', 'max:255'],
            'translations.ar.meta_title'     => ['nullable', 'string', 'max:100'],
            'translations.ar.meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        // 3) If any Arabic meta_title or meta_description is present, require AR.name
        $validator->sometimes('translations.ar.name', ['required', 'string'], function ($input) {
            // $input is a Fluent instance, so use data_get():
            $arMetaTitle       = data_get($input, 'translations.ar.meta_title');
            $arMetaDescription = data_get($input, 'translations.ar.meta_description');

            // If either AR meta_title or AR meta_description is non-empty, AR.name must be present:
            if (
                (is_string($arMetaTitle) && trim($arMetaTitle) !== '') ||
                (is_string($arMetaDescription) && trim($arMetaDescription) !== '')
            ) {
                return true;
            }
            return false;
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()->all(),
            ], 422);
        }

        // 4) Decode the Base64‐encoded logo
        $base64String = $payload['logo'];
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $typeMatch)) {
            return response()->json([
                'success' => false,
                'errors'  => ['Logo must be a valid Base64‐encoded image string.'],
            ], 422);
        }
        $imageType = strtolower($typeMatch[1]); // e.g. png, jpeg, jpg, gif

        $base64Data = substr($base64String, strpos($base64String, ',') + 1);
        $decodedImage = base64_decode($base64Data);
        if ($decodedImage === false) {
            return response()->json([
                'success' => false,
                'errors'  => ['Failed to decode Base64 image data.'],
            ], 422);
        }

        // 5) Generate a unique filename and store under public/uploads/brands/
        $filename = 'brand_' . Str::random(10) . '.' . $imageType;
        $path = 'uploads/brands/' . $filename;
        Storage::disk('public')->put($path, $decodedImage);
        $logoUrl = asset("storage/{$path}");

        // 6) Create the Brand record (always using English fields for slug and default)
        $enFields = $payload['translations']['en'];
        $brand = new Brand();
        $brand->name            = $enFields['name'];
        $brand->meta_title      = $enFields['meta_title'] ?? null;
        $brand->meta_description = $enFields['meta_description'] ?? null;
        $brand->slug            = Str::slug($enFields['name']) . '-' . Str::random(5);
        $brand->logo            = $logoUrl;
        $brand->save();

        // 7) Insert English translation
        BrandTranslation::create([
            'brand_id'        => $brand->id,
            'lang'            => 'en',
            'name'            => $enFields['name'],
            'meta_title'      => $enFields['meta_title'] ?? null,
            'meta_description' => $enFields['meta_description'] ?? null,
        ]);

        // 8) Insert Arabic if provided
        $arName = data_get($payload, 'translations.ar.name', '');
        if (is_string($arName) && trim($arName) !== '') {
            $arFields = $payload['translations']['ar'];
            BrandTranslation::create([
                'brand_id'        => $brand->id,
                'lang'            => 'ar',
                'name'            => $arFields['name'],
                'meta_title'      => $arFields['meta_title'] ?? null,
                'meta_description' => $arFields['meta_description'] ?? null,
            ]);
        }

        // 9) Return JSON success response
        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $brand->id,
                'slug'          => $brand->slug,
                'logo_url'      => $logoUrl,
                'translations'  => [
                    'en' => $enFields,
                    'ar' => array_filter($payload['translations']['ar'] ?? [], function ($v) {
                        return is_string($v) && trim($v) !== '';
                    }),
                ],
            ],
        ], 201);
    }

    public function top()
    {
        return Brand::where('top', 1)->get();
    }
}
