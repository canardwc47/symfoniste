<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{


    public function save(UploadedFile $file, string $name, string $directory): string
    {
        $filename = $name . '_' . uniqid() . '.' . $file->guessExtension();
        $file->move(
            $directory,
            $filename
        );
        return $filename;
    }

  /*  public function delete(string $filename, string $directory): void
    {
        unlink($directory . DIRECTORY_SEPARATOR . $filename);
    }*/
    public function upload(mixed $imageFile): ?string
    {
        // Validate that an image file was actually uploaded
        if (!$imageFile instanceof UploadedFile) {
            return null;
        }

        // Define allowed image mime types
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];


        // Define upload directory (adjust path as needed)
        $uploadDirectory = '/assets/image';

        // Generate a unique filename

            $filename = $this->save(
                $imageFile,
                'image',
                $uploadDirectory);


            return $filename;

    }
}