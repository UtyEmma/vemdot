<?php

namespace App\Http\Controllers\Advert;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Advert\Advert;

class AdvertController extends Controller
{
    //
    protected function createAdvertInterface(){
        return view('pages.advert.create-advert');
    }

    protected function addNewAdvert(Request $request){
        $validator = Validator::make($request->all(), [
            'banner'  => 'required',
            'banner' => 'mimes:jpg,bmp,png,jpeg'
        ]);
        if($validator->fails()) {
            Alert::error('Error', $validator->messages()->first());
            return redirect()->back();
        }

        Advert::create([
            'unique_id' => $this->createUniqueId('adverts'),
            'user_id' => $request->user()->unique_id,
            'email' => $request->email,
            'caption' => $request->caption,
            'banner' => $this->uploadImageHandler($request, 'banner', 'advert'),
            'description' => $request->description
        ]);

        Alert::success('Success', $this->returnSuccessMessage('created', 'Advert'));
        return redirect()->back();
    }

    protected function fetchAdvertInterface(){
        $adverts = Advert::orderBy('id', 'desc')->paginate($this->paginate);
        $payload = [
            'adverts' => $adverts,
        ];
        return view('pages.advert.fetch-advert', $payload);
    }

    protected function updateAdvertStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'unique_id' => 'required',
        ]);
        if($validator->fails()){
            Alert::error('Error', $validator->messages()->first());
            return redirect()->back();
        }
        //update advert status
        $advert = Advert::where('unique_id', $request->unique_id)->first();
        if($advert == null){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Advert'));
            return redirect()->back();
        }
        $advert->update([
            'status' => $request->status,
        ]);
        Alert::success('Success', $this->returnSuccessMessage('updated', 'Advert'));
        return redirect()->back();
    }

    protected function deleteAdvert(Request $request){
        $advert = Advert::where('unique_id', $request->unique_id)->first();
        if($advert == null){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Advert'));
            return redirect()->back();
        }
        $advert->delete();
        Alert::success('Success', $this->returnSuccessMessage('deleted', 'Advert'));
        return redirect()->back();
    }

    protected function fetchAdvertsForUsers(){
        $advert = Advert::where('status', $this->active)
            ->orderBy('id', 'desc')
            ->get();
        if(count($advert) > 0)
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Advert'), $advert);
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Advert'));
    }

    protected function fetchSingleAdvert($unique_id = null){
        if($unique_id == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));

        $advert = Advert::where('unique_id', $unique_id)->first();    
        if($advert != null)
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_single', 'Advert'), $advert);
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Advert'));
    }
}
