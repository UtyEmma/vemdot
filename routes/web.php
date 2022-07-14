<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Meals\CategoryController;
use App\Http\Controllers\Subscription\PlansController;
use App\Http\Controllers\Settings\SiteSettingsController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Logistic\LogisticController;
use App\Http\Controllers\Logistic\RiderController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Transaction\AdsTransaction;
use App\Http\Controllers\Transaction\WalletFundTransaction;
use App\Http\Controllers\Advert\AdvertController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Withdrawal\WithdrawalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () { return view('home'); });

Route::get('/order/invoice/{order_id}', [OrderController::class, 'downloadInvoice'])->name('order.invoice');

Route::get('/show', function(){
    return view('firebase-test');
});

Route::get('firebase', function(){
    return view('firebase-test');
});

Route::get('receipt/{order_id}', [TestController::class, 'testOrderReceipt']);

Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('login', [LoginController::class,'login']);
Route::post('register', [RegisterController::class,'register']);

Route::get('password/forget',  function () {
	return view('pages.forgot-password');
})->name('password.forget');
Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class,'reset'])->name('password.update');


Route::group(['middleware' => 'auth'], function(){
	// logout route
	Route::get('/logout', [LoginController::class,'logout']);
	Route::get('/clear-cache', [HomeController::class,'clearCache']);

	// dashboard route
	Route::get('/dashboard', [HomeController::class,'showDashoardPage'])->name('dashboard');

	// meal category handler
	Route::get('/meal-category', [CategoryController::class,'showCreateCategoryPage']);
	Route::get('/view/categories', [CategoryController::class,'viewCategories']);
	Route::post('/create/category', [CategoryController::class,'createCategory']);
	Route::post('/update/category/{id?}', [CategoryController::class,'updateCategory']);
	Route::post('/delete/category', [CategoryController::class,'deleteCategory']);
	Route::get('/edit/category/{id?}', [CategoryController::class,'viewSingleCategory']);

	// plan subscription handler
	Route::get('/subscription-plan', [PlansController::class,'showCreatePlanPage']);
	Route::get('/view/plans', [PlansController::class,'viewPlans']);
	Route::post('/create/plan', [PlansController::class,'createPlan']);
	Route::post('/update/plan/{id?}', [PlansController::class,'updatePlan']);
	Route::post('/delete/plan', [PlansController::class,'deletePlan']);
	Route::get('/edit/plan/{id?}', [PlansController::class,'editPlan']);

	//site settings
	Route::get('/site/settings', [SiteSettingsController::class,'viewSiteSettings']);
	Route::post('/update/site/settings', [SiteSettingsController::class,'updateSiteSettings']);

	//general user/vendor/logistic/rider management
	Route::post('delete/user', [VendorController::class, 'deleteUser']);
	Route::post('activate/user/status', [VendorController::class, 'activateUserStatus']);
	//vendor management
	Route::prefix('vendor')->group(function(){
        Route::get('interface/{start?}/{end?}', [VendorController::class, 'vendorsInterface']);
        Route::post('interface/by/date', [VendorController::class, 'getVendorsByDate']);
    });
	//logistic management
	Route::prefix('logistic')->group(function(){
        Route::get('interface/{start?}/{end?}', [LogisticController::class, 'logisticInterface']);
        Route::post('interface/by/date', [LogisticController::class, 'getLogisticByDate']);
    });
	//riders management
	Route::prefix('riders')->group(function(){
        Route::get('interface/{logistic?}/{start?}/{end?}', [RiderController::class, 'ridersInterface']);
        Route::post('interface/by/date', [RiderController::class, 'getRidersByDate']);
    });
	//admin management
	Route::prefix('admin')->group(function(){
        Route::get('create/interface', [AdminController::class, 'createAdminInterface']);
        Route::post('create', [AdminController::class, 'createAdminRequest']);
		Route::get('view/interface/{start?}/{end?}', [AdminController::class, 'fetchAdminInterface']);
        Route::post('interface/by/date', [AdminController::class, 'getAdminByDate']);
    });

	//transaction management
	Route::prefix('transaction')->group(function(){
		//ads section
        Route::get('ads/interface/{start?}/{end?}', [AdsTransaction::class, 'adsTransactionInterface']);
        Route::post('delete', [AdsTransaction::class, 'deletetransaction']);
		Route::post('interface/by/date', [AdsTransaction::class, 'getTransactionByDate']);
		Route::post('interface/by/type', [AdsTransaction::class, 'getTransactionByType']);
		//fund wallet section
		Route::get('fundwallet/interface/{start?}/{end?}', [WalletFundTransaction::class, 'fundWalletTransactionInterface']);
		Route::post('fundwallet/by/date', [WalletFundTransaction::class, 'getTransactionByDate']);
		Route::post('fundwallet/by/type', [WalletFundTransaction::class, 'getTransactionByType']);
    });

	//transaction management
	Route::prefix('withdrawal')->group(function(){
		//ads section
        Route::get('interface/{start?}/{end?}', [WithdrawalController::class, 'withdrawalRequestInterface']);
        Route::post('payout', [WithdrawalController::class, 'withdrawalPayout']);
        Route::get('otp/interface/{code?}/{id?}', [WithdrawalController::class, 'processOTPInterface']);
        Route::post('process-otp', [WithdrawalController::class, 'processOTP']);
        Route::post('delete', [WithdrawalController::class, 'deleteWithdrawal']);
        Route::post('decline', [WithdrawalController::class, 'declineWithdrawal']);
		Route::post('interface/by/date', [WithdrawalController::class, 'getWithdrawalalRequestByDate']);
		Route::post('interface/by/type', [WithdrawalController::class, 'getWithdrawalalRequestByType']);
		Route::prefix('histroy')->group(function(){
			Route::get('interface/{start?}/{end?}', [WithdrawalController::class, 'withdrawalInterface']);
			Route::post('interface/by/date', [WithdrawalController::class, 'getWithdrawalalByDate']);
			Route::post('interface/by/type', [WithdrawalController::class, 'getWithdrawalalByType']);
		});
    });

	//transaction management
	Route::prefix('advert')->group(function(){
		//advert section
        Route::get('create/interface', [AdvertController::class, 'createAdvertInterface']);
        Route::post('create', [AdvertController::class, 'addNewAdvert']);
        Route::get('fetch', [AdvertController::class, 'fetchAdvertInterface']);
        Route::post('update/status', [AdvertController::class, 'updateAdvertStatus']);
        Route::post('delete', [AdvertController::class, 'deleteAdvert']);
    });

    Route::prefix('users')->group(function(){
        Route::get('view/{start?}/{end?}', [UserController::class, 'index'])->name('users');
		Route::post('interface/by/date', [UserController::class, 'getUserByDate']);
        Route::get('/kyc', [UserController::class, 'fetchRequests'])->name('users.kyc');
		Route::post('update', [UserController::class, 'updateUser']);

        Route::prefix('/{user_id}')->group(function(){
            Route::get('/', [UserController::class, 'edit']);
            Route::get('/kyc', [UserController::class, 'updateKycStatus'])->name('users.kyc.update');
            Route::get('/delete', [UserController::class, 'delete']);
            Route::post('/update', [UserController::class, 'update']);
        });
    });

	//only those have manage_role permission will get access
	Route::group(['middleware' => 'can:manage_role|manage_user'], function(){
		Route::get('/roles', [RolesController::class,'index']);
		Route::get('/role/get-list', [RolesController::class,'getRoleList']);
		Route::post('/role/create', [RolesController::class,'create']);
		Route::get('/role/edit/{id}', [RolesController::class,'edit']);
		Route::post('/role/update', [RolesController::class,'update']);
		Route::get('/role/delete/{id}', [RolesController::class,'delete']);
	});


	//only those have manage_permission permission will get access
	Route::group(['middleware' => 'can:manage_permission|manage_user'], function(){
		Route::get('/permission', [PermissionController::class,'index']);
		Route::get('/permission/get-list', [PermissionController::class,'getPermissionList']);
		Route::post('/permission/create', [PermissionController::class,'create']);
		Route::get('/permission/update', [PermissionController::class,'update']);
		Route::get('/permission/delete/{id}', [PermissionController::class,'delete']);
	});

	// get permissions
	Route::get('get-role-permissions-badge', [PermissionController::class,'getPermissionBadgeByRole']);


	// permission examples
    Route::get('/permission-example', function () {
    	return view('permission-example');
    });
    // API Documentation
    Route::get('/rest-api', function () { return view('api'); });
    // Editable Datatable
	Route::get('/table-datatable-edit', function () {
		return view('pages.datatable-editable');
	});

    // Themekit demo pages
	Route::get('/calendar', function () { return view('pages.calendar'); });
	Route::get('/charts-amcharts', function () { return view('pages.charts-amcharts'); });
	Route::get('/charts-chartist', function () { return view('pages.charts-chartist'); });
	Route::get('/charts-flot', function () { return view('pages.charts-flot'); });
	Route::get('/charts-knob', function () { return view('pages.charts-knob'); });
	Route::get('/forgot-password', function () { return view('pages.forgot-password'); });
	Route::get('/form-addon', function () { return view('pages.form-addon'); });
	Route::get('/form-advance', function () { return view('pages.form-advance'); });
	Route::get('/form-components', function () { return view('pages.form-components'); });
	Route::get('/form-picker', function () { return view('pages.form-picker'); });
	Route::get('/invoice', function () { return view('pages.invoice'); });
	Route::get('/layout-edit-item', function () { return view('pages.layout-edit-item'); });
	Route::get('/layouts', function () { return view('pages.layouts'); });

	Route::get('/navbar', function () { return view('pages.navbar'); });
	Route::get('/profile', function () { return view('pages.profile'); });
	Route::get('/project', function () { return view('pages.project'); });
	Route::get('/view', function () { return view('pages.view'); });

	Route::get('/table-bootstrap', function () { return view('pages.table-bootstrap'); });
	Route::get('/table-datatable', function () { return view('pages.table-datatable'); });
	Route::get('/taskboard', function () { return view('pages.taskboard'); });
	Route::get('/widget-chart', function () { return view('pages.widget-chart'); });
	Route::get('/widget-data', function () { return view('pages.widget-data'); });
	Route::get('/widget-statistic', function () { return view('pages.widget-statistic'); });
	Route::get('/widgets', function () { return view('pages.widgets'); });

	// themekit ui pages
	Route::get('/alerts', function () { return view('pages.ui.alerts'); });
	Route::get('/badges', function () { return view('pages.ui.badges'); });
	Route::get('/buttons', function () { return view('pages.ui.buttons'); });
	Route::get('/cards', function () { return view('pages.ui.cards'); });
	Route::get('/carousel', function () { return view('pages.ui.carousel'); });
	Route::get('/icons', function () { return view('pages.ui.icons'); });
	Route::get('/modals', function () { return view('pages.ui.modals'); });
	Route::get('/navigation', function () { return view('pages.ui.navigation'); });
	Route::get('/notifications', function () { return view('pages.ui.notifications'); });
	Route::get('/range-slider', function () { return view('pages.ui.range-slider'); });
	Route::get('/rating', function () { return view('pages.ui.rating'); });
	Route::get('/session-timeout', function () { return view('pages.ui.session-timeout'); });
	Route::get('/pricing', function () { return view('pages.pricing'); });
});


Route::get('/register', function () { return view('pages.register'); });
Route::get('/login-1', function () { return view('pages.login'); });
