<?php

namespace App\Traits;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

trait FileUpload {

    public function uploadImageHandler($request, $file, $folder, $image = 'default.png',  $width = 1200, $height = 720){
        $image_hold = $image;
        if ($request->hasFile($file)) {
            $image_hold = Cloudinary::upload($request->file($file)->getRealPath(),
                [
                'folder' => 'vemdot/'.$folder,
                'transformation' => [
                    'width' => $width,
                    'height' => $height
                ]
            ])->getSecurePath();
        }
        return $image_hold;
    }

    public function uploadVideoHandler($request, $file, $folder){
        $video_hold = 'default.mp4';
        if ($request->hasFile($file)) {
            // Upload a Video File to Cloudinary with One line of Code
            $video_hold = Cloudinary::uploadVideo($request->file($file)->getRealPath(),
                [
                'folder' => 'vemdot/'.$folder,
                'transformation' => [
                    'width' => 0.4,
                    'quality' => '70%'
                ]
                ]
            )->getSecurePath();
        }
        return $video_hold;
    }

    public function uploadFileHandler($request, $file, $folder){
        $file_hold = 'default.pdf';
        if ($request->hasFile($file)) {
            // Upload any File to Cloudinary with One line of Code
            $file_hold = Cloudinary::uploadFile($request->file($file)->getRealPath(),
                ['folder' => 'vemdot/'.$folder,]
            )->getSecurePath();
        }
        return $file_hold;
    }

    function uploadFile($file, $folder = 'files'){
        $file = Cloudinary::uploadFile($file->getRealPath(), [
            'folder' => "vemdot/$folder"
        ])->getSecurePath();
        return $file;
    }


}
