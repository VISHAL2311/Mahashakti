<?php

namespace App\Helpers;

use Image as Image_Convertor;

class ImageConvertor {

    public static function convertImageToWebP($imageName, $extension, $quality = 80, $folder = false, $foldername = false) {
        if ($folder == 'folder') {
            $source = url('/') . '/assets/images/' . $foldername . '/' . $imageName . '.' . $extension;
            $destination = public_path() . '/assets/images/' . $foldername . '/' . $imageName . '.webp';
        } else {
            $source = url('/') . '/assets/images/' . $imageName . '.' . $extension;
            $destination = public_path() . '/assets/images/' . $imageName . '.webp';
        }

        $extension = pathinfo($source, PATHINFO_EXTENSION);

        if ($extension == 'jpeg' || $extension == 'jpg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($extension == 'png') {
            $image = imagecreatefrompng($source);
        }

        return imagewebp($image, $destination, $quality);
    }

}
