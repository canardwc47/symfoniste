<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{

    public function upload(UploadedFile $file, string $name, string $directory): string
    {
        $fileName = "/".$name . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($directory, $fileName);

        return $fileName;
    }

    public function delete(?string $filename, string $directory): void
    {
        if (file_exists($directory . '/' . $filename)) {
            unlink($directory . '/' . $filename);
        }
    }
}
