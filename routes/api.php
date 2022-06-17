<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\AccountActivationController;
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

  //only those have manage_user permission will get access
  Route::group(['middleware' => 'can:manage_user'], function(){
		Route::get('/users', [UserController::class,'list']);
		Route::post('/user/create', [UserController::class,'store']);
		Route::get('/user/{id}', [UserController::class,'profile']);
		Route::get('/user/delete/{id}', [UserController::class,'delete']);
		Route::post('/user/change-role/{id}', [UserController::class,'changeRole']);
	});

   Route::prefix('user')->group(function(){
        Route::get('/', [UserController::class, 'show'])->name('user.single');
        Route::post('update', [UserController::class, 'update'])->name('user.update');
        Route::post('complete-profile', [UserController::class, 'completeProfileSetup'])->name('user.setup');
    });

    // Add a middleware for role here
    Route::middleware('kyc.status')->group(function(){
        Route::prefix('meals')->group(function(){

        });
    });




});
