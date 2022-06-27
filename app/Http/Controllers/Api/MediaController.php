<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MediaController extends Controller{
    use Generics, ReturnTemplate;

    function upload(Request $request){
        $files = $request->allFiles();
        $urls = [];
        foreach ($files['media'] as $key => $file) {
            $urls[] = Cloudinary::uploadFile($file->getRealPath(),
                        ['folder' => 'vemdot/'.$request->folder,]
                        )->getSecurePath();
        }

        return Response::json([
            'urls' => $urls
        ]);
    }

    function delete(Request $request){

    }
}
