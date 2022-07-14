<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\AccountActivationController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Cards\CardController;
use App\Http\Controllers\Meals\MealsController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ResetPasswordContoller;
use App\Http\Controllers\Api\UpdatePasswordContoller;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Meals\CategoryController;
use App\Http\Controllers\Subscription\PlansController;
use App\Http\Controllers\DailySpecial\DailySpecialController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\Wallet\WalletController;
use App\Http\Controllers\Banks\BankController;
use App\Http\Controllers\Meals\FavouriteController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\User\VendorController;
use App\Http\Controllers\Logistic\LogisticController;
use App\Http\Controllers\Logistic\RiderController;
use App\Http\Controllers\Review\ReviewController;
use App\Http\Controllers\Advert\AdvertController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Withdrawal\WithdrawalController;
use App\Http\Controllers\Ticket\TicketController;

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

Route::post('/test', [TestController::class, 'testPush']);

// log users in
Route::post('login', [LoginController::class,'loginUser']);

//log rider in
Route::post('login/rider', [RiderController::class, 'loginRider']);

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

//advert handler
Route::get('/fetch/adverts', [AdvertController::class,'fetchAdvertsForUsers']);
Route::get('/fetch/single/advert/{id?}', [AdvertController::class,'fetchSingleAdvert']);

//
Route::get('/account-roles', [UserController::class, 'fetchAccountRoles']);

Route::group(['middleware' => 'auth:sanctum', 'ability:full_access'], function(){
    //log user out
    Route::get('/user-role', [UserController::class, 'fetchCurrentUserRole']);
    Route::post('upload', [MediaController::class, 'upload']);
	Route::get('logout', [Controller::class,'logoutUser']);
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

    // fund users wallet
    Route::post('/fund/user/wallet', [WalletController::class,'fundUserWallet']);

    //bank account handler
    Route::get('/fetch/all/banks', [BankController::class,'fetchAllBanks']);
    Route::get('/fetch/single/bank/{code?}', [BankController::class,'fetchSingleBank']);
    Route::post('/verify/account/number', [BankController::class,'verifyUserAccountNumber']);

    //withdrawal handler section
    Route::get('/fetch/bank/details', [WithdrawalController::class,'fetchUserBankDetails']);
    Route::get('/fetch/single/bank/detail/{id?}', [WithdrawalController::class,'fetchSingleBankDetails']);
    Route::post('/initiate/withdrawal', [WithdrawalController::class,'initiateWithdrawal']);

    Route::post('/search', [SearchController::class, 'search']);

    Route::prefix('tickets')->group(function(){
        Route::post('create', [TicketController::class, 'createTicketByUser']);
        Route::get('fetch{user_id?}', [TicketController::class, 'fectchUsersTicket']);
    });

    Route::prefix('cards')->group(function(){
        Route::get('/', [CardController::class, 'list']);
        Route::prefix('{id}')->group(function(){
            Route::get('/delete', [CardController::class, 'delete']);
        });
    });

    Route::get('/all-users', [UserController::class, 'allUsers']);

    Route::prefix('orders')->group(function(){
        Route::get('/list/{user_id?}', [OrderController::class, 'list']);

        Route::prefix('{order_id}')->group(function(){
            Route::post('/update', [OrderController::class, 'update']);
        });
    });

    Route::get('user', [UserController::class, 'show'])->name('users.current');

    Route::prefix('users')->group(function(){

        Route::prefix('{role}')->group(function(){
            Route::get('/', [UserController::class, 'list'])->name('users.list');
            Route::get('/{id}', [UserController::class, 'single'])->name('users.single');
        });

        Route::post('/update', [UserController::class, 'update'])->name('users.update');
    });

    Route::post('complete-profile', [UserController::class, 'completeProfileSetup'])->name('user.setup');

    Route::middleware('user.status:User')->group(function(){
        Route::prefix('addresses')->group(function(){
            Route::get('/', [AddressController::class, 'list']);
            Route::post('create', [AddressController::class, 'create']);
            Route::prefix('{address}')->group(function(){
                Route::get('/', [AddressController::class, 'single']);
                Route::post('update', [AddressController::class, 'update']);
                Route::get('default', [AddressController::class, 'setDefault']);
                Route::get('delete', [AddressController::class, 'delete']);
            });
        });

        Route::prefix('vendors')->group(function(){
            Route::get('/list-companies/{vendor_id}', [VendorController::class, 'listCompanies']);
        });

        Route::prefix('orders')->group(function(){
            Route::post('/create', [OrderController::class, 'create']);
        });
    });

    Route::prefix('meals')->group(function(){
        Route::get('/', [MealsController::class, 'fetchAllMeals']);

        Route::middleware('user.status:User')->group(function(){
            Route::prefix('favourites')->group(function(){
                Route::get('/', [FavouriteController::class, 'list']);
                Route::get('/{meal_id}', [FavouriteController::class, 'toggle']);
            });
        });
        Route::get('/vendor/{vendor_id?}', [MealsController::class, 'vendorMeals']);
        Route::get('/by/ads', [MealsController::class, 'fetchMealsByAds']);

        Route::middleware('kyc.status:Vendor')->group(function(){
            Route::post('/create', [MealsController::class, 'create']);
            Route::prefix('{meal_id}')->group(function(){
                Route::post('/update', [MealsController::class, 'update']);
                Route::get('/delete', [MealsController::class, 'delete']);
            });
        });

        Route::prefix('{meal_id}')->group(function(){
            Route::get('/', [MealsController::class, 'single']);
        });
    });

    // Add a middleware for role here
    Route::middleware('user.status:Vendor')->group(function(){
        Route::get('/add-company/{company_id}', [VendorController::class, 'addCompany']);
        Route::prefix('vendors')->group(function(){
            Route::get('/list-companies', [VendorController::class, 'listCompanies']);
        });
    });

    Route::get('/meal-orders/{meal_id}', [OrderController::class, 'mealOrders']);

    // logistic handler
    Route::middleware('user.status:Logistic')->group(function(){
        Route::post('create/rider', [LogisticController::class, 'createRiderRequest']);
        Route::get('fetch/all/riders/{logistic?}', [LogisticController::class, 'fetchAllRiders']);
        Route::get('fetch/rider/{uniqueId?}', [LogisticController::class, 'fetchSingleRider']);
        Route::post('update/riders/{uniqueId?}', [LogisticController::class, 'updateRiderDetails']);
        Route::delete('delete/rider/{uniqueId?}', [LogisticController::class, 'deleteRiders']);
        Route::post('riders/avaliabilty/update', [LogisticController::class, 'updateRiderAvaliablity']);
    });

    // Rider handler
    Route::middleware('user.status:Rider')->group(function(){
        Route::post('update/riders', [LogisticController::class, 'updateRiderDetails']);
    });

    // Review handler
    Route::prefix('review')->group(function(){
        Route::post('create', [ReviewController::class, 'createReview']);
        Route::get('fetch/{type?}', [ReviewController::class, 'fetchAllReview']);
    });


});
