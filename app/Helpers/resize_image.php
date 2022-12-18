<?php
/**
 * This helper give description for static block section by alias.
 * @package   Netquick
 * @version   1.00
 * @since     2016-12-07
 * @author    Vishal Agrawal
 */
namespace App\Helpers;

use App\Image;
use Config;
use File;
use Intervention\Image\Facades\Image as resizeImage;

class resize_image
{
    public static function resize($imageID = false, $width = false, $height = false)
    {

        $response = Config::get('Constant.ENV_APP_URL') . 'assets/images/default.png';

        $images = Image::getImg($imageID);

        if (!empty($images)) 
        {
            $imageName = $images->txtImageName;
            $extension = $images->varImageExtension;
            if (!empty($width) && !empty($height)) 
            {
                if (!empty($images) && $imageID != 0 && !empty($imageName) && !empty($extension) && $extension != 'svg' && $extension != 'gif') 
                {
                    $imagePath = public_path() . '/assets/images/upimages/' . $imageName . '.' . $extension;
                    if (file_exists($imagePath)) {
                        $path = public_path() . '/caches/' . $width . 'x' . $height;
                        if (file_exists($path . '/' . $imageName . '.' . $extension)) {
                            if ($height) {
                                $folderName = $width . 'x' . $height;
                            } else {
                                $folderName = $width;
                            }
                            $response = Config::get('Constant.ENV_APP_URL') . 'caches/' . $folderName . '/' . $imageName . '.' . $extension;

                        } else {
                            $img = resizeImage::make($imagePath);
                            $img->resize(intval($width), null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });

                            if (!is_dir($path)) {
                                File::makeDirectory($path, 755, true, true);
                            }

                            if ($img->save($path . '/' . $imageName . '.' . $extension)) {
                                $response = Config::get('Constant.ENV_APP_URL') . 'caches/' . $width . 'x' . $height . '/' . $imageName . '.' . $extension;
                            }
                        }
                    }
                } else {

                    $bitmapfile = public_path() . '/assets/images/upimages/' . $imageName . '.' . $extension;
                    if (realpath($bitmapfile)) {
                        $response = Config::get('Constant.ENV_APP_URL') . 'assets/images/upimages/' . $imageName . '.' . $extension;
                    }

                }

            } else {

                if ($imageID != 0 || !empty($imageID)) {
                    if (file_exists(public_path() . '/assets/images/upimages/' . $imageName . '.' . $extension)) {
                        $response = Config::get('Constant.ENV_APP_URL') . 'assets/images/upimages/' . $imageName . '.' . $extension;
                    }
                }
            }
        }
        return $response;

    }

}
