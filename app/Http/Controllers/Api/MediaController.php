<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MediaController extends Controller{
    use Generics, ReturnTemplate;

    function upload(Request $request){
        return Response::json([
            'file' => $request->file('media')
        ]);

        $file = $this->uploadFileHandler($request, 'file', $request->folder);

        return Response::json([
            'file' => $file
        ]);
    }

    function delete(Request $request){

    }
}
