<?php

namespace App\Helpers;

use Image as Image_Convertor;

class ImageConvertor {

    public static function convertImageToWebP($imageName, $extension, $quality = 80, $folder = false, $foldername = false) {
        if ($folder == 'folder') {
            $source = url('/') . '/assets/images/upimages/' . $foldername . '/' . $imageName . '.' . $extension;
            $destination = public_path() . '/assets/images/webp/' . $foldername . '/' . $imageName . '.webp';
        } else {
            $source = url('/') . '/assets/images/upimages/' . $imageName . '.' . $extension;
            $destination = public_path() . '/assets/images/webp/' . $imageName . '.webp';
        }
        
        $extension = pathinfo($source, PATHINFO_EXTENSION);

        if ($extension == 'jpeg' || $extension == 'jpg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($extension == 'png') {
            $image = imagecreatefrompng($source);
            imagepalettetotruecolor($image);
        }
      
        return imagewebp($image, $destination, $quality);
    }

}
