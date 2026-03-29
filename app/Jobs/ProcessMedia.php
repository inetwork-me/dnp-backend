<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProcessMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected Media $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function handle()
    {
        $disk       = Storage::disk('public');
        $original   = $disk->path($this->media->path);
        $mimeType   = $this->media->mime_type;
        $derivatives = [];

        // only do image processing for image/* types
        if (str_starts_with($mimeType, 'image/')) {

            // helper to mkdir if needed
            $ensureDir = function (string $fullPath) {
                $dir = dirname($fullPath);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
            };

            // 1) Thumbnail 300×300
            $derivatives['thumb'] = str_replace('.', '_thumb.', $this->media->path);
            $thumbFull = $disk->path($derivatives['thumb']);
            $ensureDir($thumbFull);
            Image::make($original)
                ->fit(300, 300)
                ->save($thumbFull);

            // 2) Preview 1200×auto
            $derivatives['preview'] = str_replace('.', '_preview.', $this->media->path);
            $previewFull = $disk->path($derivatives['preview']);
            $ensureDir($previewFull);
            Image::make($original)
                ->resize(1200, null, fn ($c) => $c->aspectRatio())
                ->save($previewFull);

            // 3) WebP conversion
            $derivatives['webp'] = preg_replace('/\.\w+$/', '.webp', $this->media->path);
            $webpFull = $disk->path($derivatives['webp']);
            $ensureDir($webpFull);
            Image::make($original)
                ->encode('webp', 85)
                ->save($webpFull);
        }

        // persist back into metadata
        $meta = $this->media->metadata ?? [];
        $meta['derivatives'] = $derivatives;
        $this->media->update(['metadata' => $meta]);
    }
}
