<?php
// app/Services/CdnService.php

namespace App\Services;

use App\Models\Media;
use App\Models\Tag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessMedia;

class CdnService
{
    /**
     * Upload a file to your CDN (public disk), create the DB record,
     * sync tags, dispatch processing job, and return the fresh Media model
     *
     * @param  UploadedFile  $file
     * @param  int|null      $folderId
     * @param  array         $tags
     * @return Media
     */
    public function uploadAndGetMediaInfo(UploadedFile $file, ?int $folderId = null, array $tags = []): Media
    {
        // 1) Store the file under a date-based folder
        $path = $file->storePublicly(
            'media/' . now()->format('Y/m/d'),
            'public'
        );

        // 2) Create the Media record
        $media = Media::create([
            'filename'  => $file->getClientOriginalName(),
            'path'      => $path,
            'mime_type' => $file->getClientMimeType(),
            'size'      => $file->getSize(),
            'folder_id' => $folderId,
            'metadata'  => [
                'alt_text'   => [],
                'caption'    => [],
                'derivatives' => []
            ],
        ]);

        // 3) Sync any tags
        if (!empty($tags)) {
            $tagIds = collect($tags)
                ->map(fn ($name) => Tag::firstOrCreate(['name' => $name])->id)
                ->all();

            $media->tags()->sync($tagIds);
        }

        // 4) Dispatch your processing job (e.g. generate thumbs, webp, previews…)
        ProcessMedia::dispatch($media);

        // 5) Return the model with its relations loaded
        return $media->load('tags', 'folder');
    }
}
