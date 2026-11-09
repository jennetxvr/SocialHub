<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'mediable_type',
    'mediable_id',
    'file_path',
    'disk',
    'file_name',
    'mime_type',
    'file_size',
    'meta',
])]
class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'meta' => 'array',
        ];
    }

    /**
     * Get the owning mediable model (Post, Message, etc.).
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the public or storage URL for this media file.
     */
    public function url(): string
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk ?? 'public');

        return $disk->url($this->file_path);
    }
}
