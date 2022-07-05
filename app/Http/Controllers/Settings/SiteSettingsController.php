<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use RealRashid\SweetAlert\Facades\Alert;

class SiteSettingsController extends Controller
{
    //
    function __construct(){

    }

    public function viewSiteSettings(Request $request){
        $appSettings = $this->getSiteSettings();

        $payload = [
            'appSettings' => $appSettings,
        ];

        if($request->wantsJson()){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('data_returned'), $appSettings);
        }else{
            return view('pages.used.site-settings', $payload);
        }
    }

    public function updateSiteSettings(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'site_name'  => 'required',
            'site_email'  => 'required',
            'site_phone'  => 'required',
            'site_domain'  => 'required',
            'referral_bonus'  => 'required',
            'token_length'  => 'required',
            'site_address'  => 'required',
            'cancellation_limit' => 'required|numeric',
            'cancellation_fee' => 'required|numeric',
            'delivery_fee' => 'required|numeric',
            'vendor_service_charge' => 'required|numeric',
            'logistics_service_charge' => 'required|numeric',
            'charge_cancellations' => 'required',
        ]);
        if($validator->fails()) {
            if($request->wantsJson()){
                return $this->returnMessageTemplate(false, $validator->messages());
            }else{
                Alert::error('Error', $validator->messages()->first());
                return redirect()->back();
            }
        }

        $appSettings = $this->getSiteSettings();
        $appSettings->site_name = $data['site_name'];
        $appSettings->site_email = $data['site_email'];
        $appSettings->site_phone = $data['site_phone'];
        $appSettings->site_domain = $data['site_domain'];
        $appSettings->referral_bonus = $data['referral_bonus'];
        $appSettings->cancellation_limit = $data['cancellation_limit'];
        $appSettings->cancellation_fee = $data['cancellation_fee'];
        $appSettings->delivery_fee = $data['delivery_fee'];
        $appSettings->vendor_service_charge = $data['vendor_service_charge'];
        $appSettings->logistics_service_charge = $data['logistics_service_charge'];
        $appSettings->charge_cancellations = $data['charge_cancellations'];
        $appSettings->token_length = $data['token_length'];
        $appSettings->site_address = $data['site_address'];
        $appSettings->account_verification = strtolower($data['account_verification']);
        $appSettings->login_alert = strtolower($data['login_alert']);
        $appSettings->welcome_message = strtolower($data['welcome_message']);
        $appSettings->send_basic_emails = strtolower($data['send_basic_emails']);
        $appSettings->site_logo = $this->uploadImageHandler($request, 'thumbnail', 'site_logo', $appSettings->site_logo, 1280, 720);
        $appSettings->save();

        if($request->wantsJson()){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Site Settings'), $appSettings);
        }else{
            Alert::success('Success', $this->returnSuccessMessage('updated', 'Site Settings'));
            return redirect()->back();
        }
    }
}
