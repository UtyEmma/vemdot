<?php

namespace App\Http\Controllers;

use App\Models\Site\SiteSettings;
use App\Models\User;
use App\Services\NotificationService;
use App\Traits\FileUpload;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ReturnTemplate, Generics,  FileUpload;

    protected $notification;

    function __construct(NotificationService $notificationService){
        $this->notification = $notificationService;
    }

    protected function user (){
        $user = Auth::user();
        return $user ? User::findOrFail($user->unique_id) : null;
    }

    protected function getSiteSettings(){
        $settings = new SiteSettings();
        return $settings->getSettings();
    }
}
