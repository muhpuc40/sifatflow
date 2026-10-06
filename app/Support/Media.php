<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Files uploaded from the admin panel go to the "public" disk (storage/app/public).
 * A DB column holds either a full link (https://...) or a path on that disk.
 * Run once:  php artisan storage:link
 */
class Media
{
    public const DISK = 'public';

    public static function isExternal(?string $value): bool
    {
        return (bool) $value && preg_match('#^https?://#i', $value) === 1;
    }

    /** Link the browser can open. Uses the current host, so it also works on 127.0.0.1. */
    public static function url(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return self::isExternal($value) ? $value : asset('storage/'.ltrim($value, '/'));
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, self::DISK);
    }

    /** Delete a stored file. Links to other websites are ignored. */
    public static function delete(?string $value): void
    {
        if ($value && ! self::isExternal($value)) {
            Storage::disk(self::DISK)->delete($value);
        }
    }
}
