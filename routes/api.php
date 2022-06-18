<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\AccountActivationController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\MealsController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ResetPasswordContoller;
use App\Http\Controllers\Api\UpdatePasswordContoller;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\PermissionController;

use App\Http\Controllers\Meals\CategoryController;
use App\Http\Controllers\Subscription\PlansController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\Users\VendorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// log users in
Route::post('login', [LoginController::class,'loginUser']);
//create new accounts for users
Route::post('register', [RegisterController::class,'register']);
//send verification token to users
Route::post('send-code', [AccountActivationController::class, 'sendActivationCode']);
//verify token
Route::post('verify-code', [AccountActivationController::class, 'verifyAndActivateAccount']);

//reset user password
Route::post('send-reset-code', [ResetPasswordContoller::class, 'passwordResetCode']);
Route::post('resend-reset-code', [ResetPasswordContoller::class, 'resendResetCode']);
Route::post('verify-reset-code', [ResetPasswordContoller::class, 'verifySentResetCode']);
Route::post('reset-password', [ResetPasswordContoller::class, 'resetPassword']);


Route::group(['middleware' => 'auth:sanctum'], function(){
    //log user out
    Route::post('upload', [MediaController::class, 'upload']);
	Route::get('logout', [LoginController::class,'logoutUser']);
	//update user password
	Route::post('update-password', [UpdatePasswordContoller::class, 'updateUserPassword']);

	Route::get('profile', [AuthController::class,'profile']);
	Route::post('change-password', [AuthController::class,'changePassword']);
	Route::post('update-profile', [AuthController::class,'updateProfile']);

	// meal category handler
	Route::post('/create/category', [CategoryController::class,'createCategory']);
	Route::get('/view/categories', [CategoryController::class,'viewCategories'])->name('view/categories');
	Route::post('/update/category/{id?}', [CategoryController::class,'updateCategory']);
	Route::get('/edit/category/{id?}', [CategoryController::class,'viewSingleCategory']);
	Route::post('/delete/category', [CategoryController::class,'deleteCategory']);

	// plan subscription handler
	Route::get('/view/plans', [PlansController::class,'viewPlans']);
	Route::post('/create/plan', [PlansController::class,'createPlan']);
	Route::post('/update/plan/{id?}', [PlansController::class,'updatePlan']);
	Route::post('/delete/plan', [PlansController::class,'deletePlan']);
	Route::get('/edit/plan/{id?}', [PlansController::class,'editPlan']);


    Route::get('user', [UserController::class, 'show'])->name('users.current');
    Route::prefix('users')->group(function(){
        Route::prefix('{role}')->group(function(){
            Route::get('/', [UserController::class, 'list'])->name('users.list');
            Route::get('/{id}', [UserController::class, 'single'])->name('users.single');
        });
        Route::post('update', [UserController::class, 'update'])->name('users.update');
    });

    Route::middleware('user.status:User')->group(function(){

        Route::prefix('addresses')->group(function(){
            Route::post('/', [AddressController::class, 'list']);
            Route::post('create', [AddressController::class, 'create']);
            Route::post('update', [AddressController::class, 'update']);
            Route::post('delete', [AddressController::class, 'delete']);
        });


    });

    // Add a middleware for role here
    Route::middleware('user.status:Vendor')->group(function(){
        Route::post('complete-profile', [UserController::class, 'completeProfileSetup'])->name('user.setup');

        Route::middleware('kyc.status:Vendor')->group(function(){
            Route::prefix('meals')->group(function(){
                Route::get('/', [MealsController::class, 'fetchAllMeals']);
                Route::post('/create', [MealsController::class, 'create']);
                Route::post('/update/{meal_id}', [MealsController::class, 'update']);
                Route::get('/vendor/{vendor_id?}', [MealsController::class, 'vendorMeals']);
            });


        });

    });




});
