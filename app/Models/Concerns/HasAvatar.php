<?php

namespace App\Models\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Reusable avatar storage logic for any model that has an `avatar` column.
 *
 * Files are stored directly under the public directory so they are served
 * without a storage:link symlink:
 *
 *   public/assets/avatars/{userId}/{hash}.{ext}
 *
 * The `avatar` column stores the path relative to /public, e.g.
 *   "assets/avatars/12/9f8a...e1.png"
 * so it can be rendered with asset($model->avatar).
 *
 * The trait does NOT call save() — caller assigns the returned path and saves.
 */
trait HasAvatar
{
    /**
     * Move the uploaded image into public/assets/avatars and return its
     * path relative to /public. Does NOT touch $this->avatar.
     */
    public function storeAvatar(UploadedFile $file, ?int $userId = null): string
    {
        $id = $userId ?? $this->id ?? 'tmp';
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        $hash = Str::random(40);

        $relativeDir = "assets/avatars/{$id}";
        $dir = public_path($relativeDir);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $filename = "{$hash}.{$ext}";
        $file->move($dir, $filename);

        return "{$relativeDir}/{$filename}";
    }

    /**
     * Delete current avatar file from /public (if any) and null the column.
     * Caller is responsible for save().
     */
    public function deleteAvatar(): void
    {
        if (! empty($this->avatar) && file_exists(public_path($this->avatar))) {
            @unlink(public_path($this->avatar));
        }
        $this->avatar = null;
    }

    /**
     * Convenience: replace old with new in one call. Caller still saves.
     */
    public function replaceAvatar(UploadedFile $file, ?int $userId = null): string
    {
        if (! empty($this->avatar) && file_exists(public_path($this->avatar))) {
            @unlink(public_path($this->avatar));
        }
        $this->avatar = $this->storeAvatar($file, $userId);
        return $this->avatar;
    }
}
