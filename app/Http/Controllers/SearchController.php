<?php

namespace App\Http\Controllers;

use App\Models\Meal\Meal;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller {

    function search(Request $request){
        $keyword = $request->keyword;
        $vendors = User::where('name', 'LIKE', "%$keyword%")->whereRelation('userRole', 'name', 'Vendors')->get();
        $meals = Meal::where('name', 'LIKE', "%$keyword%")->where('availability', $this->yes)->get();

        return $this->returnMessageTemplate(true, "", [
            'meals' => $meals,
            'vendors' => $vendors
        ]);
    }

}
