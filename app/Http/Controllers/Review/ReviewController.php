<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Review\Review;

class ReviewController extends Controller
{
    //

    protected function createReview(Request $request){
        $data = $request->all();
        $user = $request->user();
        $validator = Validator::make($data, [
            'type' => 'required',
            'data_id' => 'required',
            'rate' => 'required',
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->messages()); 
        if($data['rate'] > 5 || $data['rate'] < 0)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('exceed_rate'));

        $review = Review::where('user_id', $user->unique_id)
            ->where('type', $data['type'])
            ->where('data_id', $data['data_id'])
            ->first();
        if($review != null){
            $review->update([
                'rate' => $data['rate'],
                'comment' => $data['comment'],
            ]);
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Review'));
        }else{
            Review::create([
                'unique_id' => $this->createUniqueId('reviews'),
                'user_id' => $user->unique_id,
                'type' => $data['type'],
                'data_id' => $data['data_id'],
                'rate' => $data['rate'],
                'comment' => $data['comment'],
            ]);
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('created', 'Review'));
        }
    }

    protected function fetchAllReview($type = null){
        if($type == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));

        $reviews = Review::where('type', $type)->get();
        if($reviews->count() == 0)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Reviews'));
            
        foreach($reviews as $review){
            $review->user;
        }

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Reviews'), $reviews);  
    }
}

