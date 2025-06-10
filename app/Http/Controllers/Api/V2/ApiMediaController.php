<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Models\Media;
use App\Jobs\ProcessMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiMediaController extends Controller
{
    /**
     * GET /api/v2/media
     */
    public function index(Request $req)
    {
        $q = Media::query();

        if ($req->filled('filename')) {
            $q->where('filename', 'like', "%{$req->filename}%");
        }
        if ($req->filled('folder_id')) {
            $q->where('folder_id', $req->folder_id);
        }
        if ($req->filled('date_from')) {
            $q->whereDate('created_at', '>=', $req->date_from);
        }
        if ($req->filled('date_to')) {
            $q->whereDate('created_at', '<=', $req->date_to);
        }
        if ($req->filled('tags')) {
            $q->whereHas('tags', fn ($b) => $b->whereIn('name', $req->tags));
        }

        $paginated = $q
            ->with('tags', 'folder')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // ← Explicitly return JSON
        return response()->json($paginated, 200);
    }

    /**
     * POST /api/v2/media
     */
    public function store(StoreMediaRequest $req)
    {
        $out = [];
        foreach ($req->file('files') as $file) {
            $path = $file->storePublicly(
                'media/' . now()->format('Y/m/d'),
                'public'
            );

            $media = Media::create([
                'filename'  => $file->getClientOriginalName(),
                'path'      => $path,
                'mime_type' => $file->getClientMimeType(),
                'size'      => $file->getSize(),
                'folder_id' => $req->folder_id,
                'metadata'  => ['alt_text' => [], 'caption' => []],
            ]);

            if ($req->filled('tags')) {
                $media->tags()->sync(
                    collect($req->tags)
                        ->map(fn ($t) => \App\Models\Tag::firstOrCreate(['name' => $t])->id)
                );
            }

            ProcessMedia::dispatch($media);
            $out[] = $media;
        }

        return response()->json($out, 201);
    }

    /**
     * GET /api/v2/media/{media}
     */
    public function show(Media $media)
    {
        return response()->json(
            $media->load('tags', 'folder'),
            200
        );
    }

    /**
     * PUT /api/v2/media/{media}
     */
    public function update(Request $req, Media $media)
    {
        $media->update($req->only('metadata'));
        return response()->json(
            $media->fresh('tags', 'folder'),
            200
        );
    }

    /**
     * DELETE /api/v2/media/{media}
     */
    public function destroy(Media $media)
    {
        $paths = array_filter([
            $media->path,
            $media->metadata['derivatives']['thumb']   ?? null,
            $media->metadata['derivatives']['preview'] ?? null,
            $media->metadata['derivatives']['webp']    ?? null,
        ], fn ($p) => is_string($p) && $p !== '');

        Storage::disk('public')->delete($paths);
        $media->delete();

        return response()->json(null, 204);
    }

    /**
     * DELETE /api/v2/media/bulk
     */
    public function bulkDestroy(Request $req)
    {
        dd('ss');
        $ids = $req->input('ids', []);
        $items = Media::whereIn('id', $ids)->get();

        foreach ($items as $item) {
            $this->destroy($item);
        }

        return response()->json(null, 204);
    }
}
