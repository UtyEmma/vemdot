<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use App\Traits\FileUpload;
use App\Models\Site\SiteSettings;

use RealRashid\SweetAlert\Facades\Alert;

class SiteSettingsController extends Controller
{
    use Generics, ReturnTemplate, FileUpload;
    //
    function __construct(SiteSettings $appSettings){
        $this->appSettings = $appSettings;
    }

    public function viewSiteSettings(Request $request){
        $appSettings = $this->appSettings->getSettings();

        $payload = [
            'appSettings' => $appSettings,
        ];

        if($request->wantsJson()){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('data_returned'), $appSettings);
        }else{
            return view('pages.used.site-settings', $payload);
        }
    }
}
