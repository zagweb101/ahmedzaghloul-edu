<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    /** @var list<string> */
    public const IMAGE_RULES = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

    /** @var list<string> */
    public const PDF_RULES = ['nullable', 'file', 'mimes:pdf', 'max:10240'];

    public const MAX_POST_IMAGES = 5;

    /** @var list<string> */
    public const POST_IMAGES_RULES = ['nullable', 'array', 'max:' . self::MAX_POST_IMAGES];

    /** @var list<string> */
    public const POST_IMAGE_ITEM_RULES = ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

    public function storePublicImage(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    /**
     * @param  list<UploadedFile>  $files
     * @return list<string>
     */
    public function storePublicImages(array $files, string $directory): array
    {
        return array_map(
            fn (UploadedFile $file) => $this->storePublicImage($file, $directory),
            $files,
        );
    }

    public function storePrivateDocument(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'local');
    }

    public function delete(?string $path, string $disk = 'public'): void
    {
        if ($path) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function publicUrl(?string $path): ?string
    {
        return $path ? asset('storage/' . $path) : null;
    }

    public function replacePublicImage(
        ?string $currentPath,
        ?UploadedFile $file,
        string $directory,
        bool $remove = false,
    ): ?string {
        if ($remove) {
            $this->delete($currentPath);

            return null;
        }

        if (! $file) {
            return $currentPath;
        }

        $this->delete($currentPath);

        return $this->storePublicImage($file, $directory);
    }

    public function replacePrivateDocument(
        ?string $currentPath,
        ?UploadedFile $file,
        string $directory,
        bool $remove = false,
    ): ?string {
        if ($remove) {
            $this->delete($currentPath, 'local');

            return null;
        }

        if (! $file) {
            return $currentPath;
        }

        $this->delete($currentPath, 'local');

        return $this->storePrivateDocument($file, $directory);
    }
}
