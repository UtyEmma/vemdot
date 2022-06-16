<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $notification;

    function __construct(NotificationService $notificationService){
        $this->notification = $notificationService;
    }

    protected function user (){
        $user = Auth::user();
        return $user ? User::findOrFail($user->unique_id) : null;
    }
}
