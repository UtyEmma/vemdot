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
use App\Http\Controllers\Controller;

use App\Http\Controllers\Meals\CategoryController;
use App\Http\Controllers\Subscription\PlansController;
use App\Http\Controllers\DailySpecial\DailySpecialController;
use App\Http\Controllers\Subscription\SubscriptionController;

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
//2fa handler
Route::post('user/2fa/verify', [LoginController::class, 'processUserlogin']);
Route::get('/payment/callback', [Controller::class,'verifyPayment']);

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


Route::group(['middleware' => 'auth:sanctum', 'ability:full_access'], function(){
    //log user out
    Route::post('upload', [MediaController::class, 'upload']);
	Route::get('logout', [LoginController::class,'logoutUser']);
	//update user password
	Route::post('update-password', [UpdatePasswordContoller::class, 'updateUserPassword']);

	//user profile
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

	// daily specials
	Route::post('/create/daily/special', [DailySpecialController::class,'createDailySpecial']);
	Route::get('/fetch/daily/special', [DailySpecialController::class,'fetchDailySpecials']);
	Route::get('/fetch/daily/special/by/vendor/{user_id?}', [DailySpecialController::class,'fetchDailySpecialsByVendor']);
	Route::get('/fetch/single/daily/special/{id?}', [DailySpecialController::class,'fetchSingleDailySpecial']);
	Route::delete('/delete/daily/special/{id?}', [DailySpecialController::class,'deleteDailySpecial']);

	// subscription handler 
	Route::post('/create/vendor/subscription', [SubscriptionController::class,'createVendorSubscription']);

  //only those have manage_user permission will get access
  Route::group(['middleware' => 'can:manage_user'], function(){
		Route::get('/users', [UserController::class,'list']);
		Route::post('/user/create', [UserController::class,'store']);
		Route::get('/user/{id}', [UserController::class,'profile']);
		Route::get('/user/delete/{id}', [UserController::class,'delete']);
		Route::post('/user/change-role/{id}', [UserController::class,'changeRole']);
	});

  Route::get('user', [UserController::class, 'show'])->name('users.current');
  Route::prefix('users')->group(function(){
      Route::prefix('{role}')->group(function(){
          Route::get('/', [UserController::class, 'list'])->name('users.list');
          Route::get('/{id}', [UserController::class, 'single'])->name('users.single');
      });
      Route::post('update', [UserController::class, 'update'])->name('users.update');
      Route::post('complete-profile', [UserController::class, 'completeProfileSetup'])->name('user.setup');
  });

    Route::middleware('user.status:User')->group(function(){

        Route::prefix('addresses')->group(function(){
            Route::post('/', [AddressController::class, 'list']);
            Route::post('create', [AddressController::class, 'create']);

            Route::prefix('{id}')->group(function(){
                Route::post('update', [AddressController::class, 'update']);
                Route::post('delete', [AddressController::class, 'delete']);
            });
        });
    });

    Route::prefix('meals')->group(function(){
        Route::middleware('user.status:User')->group(function(){
            Route::get('/', [MealsController::class, 'fetchAllMeals']);
        });

        Route::get('/vendor/{vendor_id?}', [MealsController::class, 'vendorMeals']);

        Route::middleware('kyc.status:Vendor')->group(function(){
            Route::post('/create', [MealsController::class, 'create']);
            Route::prefix('{meal_id}')->group(function(){
                Route::get('/', [MealsController::class, 'single']);
                Route::post('/update', [MealsController::class, 'update']);
                Route::get('/delete', [MealsController::class, 'delete']);
            });
        });
    });

    // Add a middleware for role here
    Route::middleware('user.status:Vendor')->group(function(){
        Route::post('complete-profile', [UserController::class, 'completeProfileSetup'])->name('user.setup');


    });




});
