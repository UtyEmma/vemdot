<?php

namespace App\Http\Controllers\Meals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use App\Traits\FileUpload;
use App\Models\Meal\MealCategory;

use RealRashid\SweetAlert\Facades\Alert;

class CategoryController extends Controller
{
    use Generics, ReturnTemplate, FileUpload;
    //
    function __construct(MealCategory $mealCategory){
        $this->mealCategory = $mealCategory;
    }

    public function showCreateCategoryPage(){
        return view('pages.used.add-meal-category');
    }

    public function createCategory(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'name'  => 'required',
        ]);
        if($validator->fails()) {
            if($request->wantsJson()){
                return $this->returnMessageTemplate(false, $validator->messages());
            }else{
                Alert::error('Error', $validator->messages()->first());
                return redirect()->back();
            }
        }

        $category = $this->mealCategory->getMealCategory([
            ['name', $data['name']],
        ]);

        if($category != null){
            $category->name = $data['name'] ? $data['name'] : null;
            $category->description = $data['description'] ? $data['description'] : null;
            $category->status = $this->active; 
            $category->thumbnail = $this->uploadImageHandler($request, 'thumbnail', 'meal_category', $category->thumbnail, 1280, 720); 
            $category->save();
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_updated'), $category);
            }else{
                Alert::success('Success', $this->returnSuccessMessage('successful_updated'));
                return redirect()->to('view/categories');
            }
        }else{
            $categories = new MealCategory();
            $categories->unique_id = $this->createUniqueId('meal_categories', 'unique_id');
            $categories->name = $data['name'];
            $categories->description = $data['description'];
            $categories->status = $this->active; 
            $categories->thumbnail = $this->uploadImageHandler($request, 'thumbnail', 'meal_category', 'default.png', 1280, 720); 
            $categories->save();
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_creation'), $categories);
            }else{
                Alert::success('Success', $this->returnSuccessMessage('successful_creation'));
                return redirect()->to('view/categories');
            }
        }

        if($request->wantsJson()){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }else{
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }
    }

    public function viewCategories(Request $request){
        $category = $this->mealCategory->paginateMealCategorys($this->paginate, [
            ['status', $this->active],
        ]);

        $payload = [
            'category' => $category,
        ];

        if($request->wantsJson()){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('data_returned'), $category);
        }else{
            return view('pages.used.view-category-list', $payload);
        }
    }

    public function viewSingleCategory(Request $request, $unique_id = null){
        if($unique_id != null){
            $category = $this->mealCategory->getMealCategory([
                ['unique_id', $unique_id],
            ]);
    
            $payload = [
                'category' => $category,
            ];
    
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('data_returned'), $category);
            }else{
                return view('pages.used.edit-meal-category', $payload);
            }
        }else{
            if($request->wantsJson()){
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
            }else{
                Alert::error('Error', $this->returnErrorMessage('unknown_error'));
                return redirect()->back();
            }
        }
    }

    public function updateCategory(Request $request, $unique_id = null){
        $data = $request->all();

        $validator = Validator::make($data, [
            'name'  => 'required',
        ]);
        if($validator->fails()) {
            if($request->wantsJson()){
                return $this->returnMessageTemplate(false, $validator->messages());
            }else{
                Alert::error('Error', $validator->messages()->first());
                return redirect()->back();
            }
        }

        $category = $this->mealCategory->getMealCategory([
            ['unique_id', $unique_id],
        ]);

        if($category != null){
            $category->name = $data['name'] ? $data['name'] : null;
            $category->description = $data['description'] ? $data['description'] : null;
            $category->status = $this->active; 
            $category->thumbnail = $this->uploadImageHandler($request, 'thumbnail', 'meal_category', $category->thumbnail, 1280, 720); 
            $category->save();
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_updated'), $category);
            }else{
                Alert::success('Success', $this->returnSuccessMessage('successful_updated'));
                return redirect()->to('view/categories');
            }
        }

        if($request->wantsJson()){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }else{
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->to('view/categories');
        }
    }

    public function deleteCategory(Request $request){
        $data = $request->all();

        $category = $this->mealCategory->getMealCategory([
            ['unique_id', $data['unique_id']],
        ]);

        if($category != null){
            
            $category->delete();
            
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_deleted'));
            }else{
                Alert::success('Success', $this->returnSuccessMessage('successful_deleted'));
                return redirect()->to('view/categories');
            }
        }

        if($request->wantsJson()){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }else{
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }
    }
}
