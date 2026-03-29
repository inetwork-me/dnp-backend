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

        $brand_query->latest();

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
        $logoUrl = Storage::url($path);

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

    /**
     * DELETE /api/v2/brands/{id}
     *
     * Deletes a brand and all its translations, but only if the brand
     * has no associated products. Also removes the logo file from storage.
     */
    public function destroy($id)
    {
        // 1) Look up the brand (404 if not found)
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'success' => false,
                'errors'  => ['Brand not found.'],
            ], 404);
        }

        // 2) Check if any products are linked to this brand
        //    (Assuming your Brand model has a "products()" relationship)
        // TODO we need this to check if there's related products to the brand
        // if ($brand->products()->exists()) {
        //     return response()->json([
        //         'success' => false,
        //         'errors'  => ['Cannot delete this brand because it has associated products.'],
        //     ], 400);
        // }

        // 3) Delete logo file from "public" disk, if it exists
        //    We assume $brand->logo contains a Storage::url() value like "/storage/uploads/brands/brand_xxx.jpeg"
        $logoUrl = $brand->logo;
        if ($logoUrl) {
            // Remove any leading "/storage/" so we can delete from "public" disk
            $relativePath = preg_replace('#^/storage/#', '', $logoUrl);
            if ($relativePath && Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }

        // 4) Delete all translations for this brand
        BrandTranslation::where('brand_id', $brand->id)->delete();

        // 5) Finally, delete the brand record itself
        $brand->delete();

        // 6) Return a 200 OK response
        return response()->json([
            'success' => true,
            'message' => 'Brand and its translations have been deleted.',
        ], 200);
    }

    /**
     * GET /api/v2/brands/{id}
     * 
     * Return a single brand, including both English & Arabic translations.
     */
    public function show($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'success' => false,
                'errors'  => ['Brand not found.'],
            ], 404);
        }

        // Build translations array:
        $translations = BrandTranslation::where('brand_id', $id)
            ->get()
            ->keyBy('lang')
            ->map(function ($t) {
                return [
                    'name'             => $t->name,
                    'meta_title'       => $t->meta_title,
                    'meta_description' => $t->meta_description,
                ];
            })
            ->toArray();

        // Make sure both 'en' and 'ar' keys exist (even if empty)
        $enTrans = $translations['en'] ?? ['name' => '', 'meta_title' => null, 'meta_description' => null];
        $arTrans = $translations['ar'] ?? ['name' => '', 'meta_title' => null, 'meta_description' => null];

        // Ensure logo is a proper "/storage/…" path
        $logo = $brand->logo;
        if (!Str::startsWith($logo, '/storage/')) {
            $logo = Storage::url(ltrim($logo, '/'));
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $brand->id,
                'slug'         => $brand->slug,
                'name'         => $brand->name,
                'logo'         => $logo,
                'translations' => [
                    'en' => $enTrans,
                    'ar' => $arTrans,
                ],
            ],
            'status'  => 200,
        ], 200);
    }


    /**
     * PUT /api/v2/brands/{id}
     *
     * Update an existing brand. Replaces its logo if a new Base64 string is provided,
     * replaces translations, and updates name/slug accordingly.
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'success' => false,
                'errors'  => ['Brand not found.'],
            ], 404);
        }

        // 1) Validation rules (very similar to store())
        $payload = $request->all();
        $validator = Validator::make($payload, [
            // Logo is nullable on update; only required if user is changing it
            'logo'                            => ['nullable', 'string'],
            'translations.en.name'            => ['required', 'string', 'max:255'],
            'translations.en.meta_title'      => ['nullable', 'string', 'max:100'],
            'translations.en.meta_description' => ['nullable', 'string', 'max:500'],
            'translations.ar.name'            => ['nullable', 'string', 'max:255'],
            'translations.ar.meta_title'      => ['nullable', 'string', 'max:100'],
            'translations.ar.meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        // Arabic “sometimes required” logic (same as in store())
        $validator->sometimes('translations.ar.name', ['required', 'string'], function ($input) {
            $arMetaTitle       = data_get($input, 'translations.ar.meta_title');
            $arMetaDescription = data_get($input, 'translations.ar.meta_description');
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

        // 2) If a new 'logo' Base64 string is provided, delete old file and store new
        if (isset($payload['logo']) && is_string($payload['logo']) && $payload['logo'] !== '') {
            $base64String = $payload['logo'];

            // Validate Base64 format
            if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $typeMatch)) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['Logo must be a valid Base64‐encoded image string.'],
                ], 422);
            }
            $imageType   = strtolower($typeMatch[1]); // png, jpeg, etc.
            $base64Data  = substr($base64String, strpos($base64String, ',') + 1);
            $decodedImage = base64_decode($base64Data);
            if ($decodedImage === false) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['Failed to decode Base64 image data.'],
                ], 422);
            }

            // Delete old logo file from “public” disk if it exists
            $oldLogo = $brand->logo;
            if ($oldLogo) {
                // Remove leading "/storage/" to get relative path:
                $relativeOld = preg_replace('#^/storage/#', '', $oldLogo);
                if ($relativeOld && Storage::disk('public')->exists($relativeOld)) {
                    Storage::disk('public')->delete($relativeOld);
                }
            }

            // Store new file
            $filename     = 'brand_' . Str::random(10) . '.' . $imageType;
            $relativePath = 'uploads/brands/' . $filename;
            Storage::disk('public')->put($relativePath, $decodedImage);

            // Get the new "/storage/..." URL
            $newLogoUrl = Storage::url($relativePath);
            $brand->logo = $newLogoUrl;
        }

        // 3) Update the brand’s basic columns (name, meta fields, slug)
        $enFields           = $payload['translations']['en'];
        $brand->name        = $enFields['name'];
        $brand->meta_title       = $enFields['meta_title'] ?? null;
        $brand->meta_description = $enFields['meta_description'] ?? null;
        // regenerate slug from updated English name:
        $brand->slug = Str::slug($enFields['name']) . '-' . Str::random(5);
        $brand->save();

        // 4) Update translations—delete old and insert new
        BrandTranslation::where('brand_id', $brand->id)->delete();

        // Insert English translation
        BrandTranslation::create([
            'brand_id'        => $brand->id,
            'lang'            => 'en',
            'name'            => $enFields['name'],
            'meta_title'      => $enFields['meta_title'] ?? null,
            'meta_description' => $enFields['meta_description'] ?? null,
        ]);

        // Insert Arabic only if provided
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

        // 5) Return updated brand data (mimic show()’s shape)
        $translations = BrandTranslation::where('brand_id', $brand->id)
            ->get()
            ->keyBy('lang')
            ->map(function ($t) {
                return [
                    'name'             => $t->name,
                    'meta_title'       => $t->meta_title,
                    'meta_description' => $t->meta_description,
                ];
            })
            ->toArray();

        $enTrans = $translations['en'] ?? ['name' => '', 'meta_title' => null, 'meta_description' => null];
        $arTrans = $translations['ar'] ?? ['name' => '', 'meta_title' => null, 'meta_description' => null];

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $brand->id,
                'slug'         => $brand->slug,
                'name'         => $brand->name,
                'logo'         => $brand->logo,
                'translations' => [
                    'en' => $enTrans,
                    'ar' => $arTrans,
                ],
            ],
            'status'  => 200,
        ], 200);
    }
    public function top()
    {
        return Brand::where('top', 1)->get();
    }
}
