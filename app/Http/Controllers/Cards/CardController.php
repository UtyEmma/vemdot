<?php

namespace App\Http\Controllers\Cards;

use App\Http\Controllers\Controller;
use App\Http\Requests\Card\CreateCardRequest;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller{

    function list(){
        $user = $this->user();
        return $this->returnMessageTemplate(true, '', [
            'cards' => $user->cards
        ]);
    }

    function delete($id){
        if(!$card = Card::find($id))
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Card'));
        $card->delete();
        $user = $this->user();

        return $this->returnMessageTemplate(true, 'Card Deleted Successfully', $user->cards);
    }

}
