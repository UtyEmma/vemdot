<?php

namespace App\Http\Controllers\DailySpecial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Meal\Meal;
use App\Models\DailySpecial\DailySpecial;

class DailySpecialController extends Controller
{
    //
    public function createDailySpecial(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'meal_id'  => 'required',
        ]);
        if($validator->fails()) {
            return $this->returnMessageTemplate(false, $validator->messages());
        }

        $meal = Meal::where('unique_id', $data['meal_id'])->first();
        if($meal == null){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Meal'));
        }
        if($meal->availability == $this->no){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('meal_availability'));
        }

        DailySpecial::create([
            'unique_id' => $this->createUniqueId('daily_specials'),
            'user_id' => $this->user()->unique_id,
            'meal_id' => $data['meal_id'],
            'status' => 'inprogress',
            'caption' => $data['caption'],
        ]);

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('created', 'Daily Specials'));
    }

    public function fetchDailySpecials(){        
        $dailySpecial = DailySpecial::where('status', 'inprogress')
        ->orderBy('id', 'desc')
        ->paginate($this->paginate);

        if(count($dailySpecial) > 0){
            foreach($dailySpecial as $each_special){
                $each_special->meals;
                $each_special->meals->category;
                $each_special->owner;
            }
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Daily Specials'), $dailySpecial);
        }else{
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Daily Specials'));
        }
    }

    public function fetchDailySpecialsByVendor($unique_id = null){
        $user_id = ($unique_id == null) ? $this->user()->unique_id : $unique_id;
        
        $dailySpecial = DailySpecial::where('status', 'inprogress')
        ->where('user_id', $user_id)
        ->orderBy('id', 'desc')
        ->paginate($this->paginate);

        if(count($dailySpecial) > 0){
            foreach($dailySpecial as $each_special){
                $each_special->meals;
                $each_special->meals->category;
                $each_special->owner;
            }
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Daily Specials'), $dailySpecial);
        }else{
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Daily Specials'));
        }
    }

    public function fetchSingleDailySpecial($unique_id = null){
        if($unique_id == null){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }
        $dailySpecial = DailySpecial::where('unique_id', $unique_id)->first();
    
        if($dailySpecial != null){
            $dailySpecial->meals;
            $dailySpecial->meals->category;
            $dailySpecial->owner;
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_single', 'Daily Specials'), $dailySpecial);
        }else{
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Daily Specials'));
        }
    }

    public function deleteDailySpecial($unique_id = null){
        if($unique_id == null){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }

        $dailySpecial = DailySpecial::where('unique_id', $unique_id)->delete();
        if($dailySpecial != 0){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('deleted', 'Daily Specials'));
        }else{
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }
    }
}
