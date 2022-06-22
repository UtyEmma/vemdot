<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{


    public function index()
    {
        return view('home');
    }

    public function showDashoardPage(){
        return view('pages.dashboard');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        return view('clear-cache');
    }
}
