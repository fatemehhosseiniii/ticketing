<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{


    public function setFile($file, $path, $remove = false): string|null
    {

        if ($remove && !empty($path))
            self::deleteFile($path);
        elseif ($file)
            $result = self::uploadFile($file, $path);

        return $result ?? null;

    }

    private static function uploadFile($file, $path = null): string
    {
        $extension = $file->getClientOriginalExtension();
        $newFileName = Str::uuid()->toString() . '.' . $extension;

        Storage::putFileAs($path, $file, $newFileName);
        $filePath = Storage::url($path . '/' . $newFileName);

        return str_contains($filePath, 'storage') ? substr($filePath, strpos($filePath, 'storage') - 1) : $filePath;

    }

    private static function deleteFile($filePath): void
    {
        $filePath=str_replace('storage','', $filePath);
        /*check has */
        if (Storage::exists($filePath))
            Storage::delete($filePath);
    }

}
