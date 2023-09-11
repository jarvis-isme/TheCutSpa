<?php

use App\Http\Controllers\CategoryController as CategoryC;
use App\Http\Controllers\FileController as FileC;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagerController as ManagerC;
use App\Http\Controllers\UserController as UserC;
use App\Http\Controllers\ProductController as ProductC;
use App\Http\Controllers\ProductOrderController as ProductOrderC;
use App\Http\Controllers\ServiceCategoryController as ServiceCategoryC;
use App\Http\Controllers\ServiceController as ServiceC;
use App\Http\Controllers\ServiceOrderController as ServiceOrderC;
use App\Http\Controllers\StoreController as StoreC;
use App\Models\Enums\RoleEnum;
use Illuminate\Support\Facades\Route;
use Ladumor\OneSignal\OneSignal;

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

Route::post(UserC::API_URL_LOGIN, [UserC::class, UserC::METHOD_LOGIN]);
Route::post(UserC::API_URL_REGISTER, [UserC::class, UserC::METHOD_REGISTER]);
Route::post(UserC::API_URL_OTP, [UserC::class, UserC::METHOD_SEND_OTP]);
Route::post(UserC::API_URL_RESET_PASSWORD, [UserC::class, UserC::METHOD_RESET_PASSWORD]);

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['prefix' => UserC::PREFIX], function () {
        Route::get(UserC::API_URL_LOGOUT, [UserC::class, UserC::METHOD_LOGOUT]);
        Route::post(UserC::API_URL_CHANGE_PASSWORD, [UserC::class, UserC::METHOD_CHANGE_PASSWORD]);
        Route::get(UserC::API_URL_GET_USER_PROFILE, [UserC::class, UserC::METHOD_GET_PROFILE]);
        Route::post(UserC::API_URL_UPDATE_USER_PROFILE, [UserC::class, UserC::METHOD_UPDATE_PROFILE]);
        Route::get(UserC::API_URL_GET_CART, [UserC::class, UserC::METHOD_GET_CART]);
        Route::post(UserC::API_URL_UPDATE_CART, [UserC::class, UserC::METHOD_UPDATE_CART]);

        Route::post(UserC::API_URL_GET_PRODUCT_ORDERS, [UserC::class, UserC::METHOD_GET_PRODUCT_ORDERS]);
        Route::post(UserC::API_URL_GET_SERVICE_ORDERS, [UserC::class, UserC::METHOD_GET_SERVICE_ORDERS]);
    });

    Route::group(['prefix' => FileC::PREFIX], function () {
        Route::post(FileC::API_URL_UPLOAD_FILE_S3, [FileC::class, FileC::METHOD_UPLOAD_FILE_S3]);
    });

    Route::group(['prefix' => CategoryC::PREFIX], function () {
        Route::get(CategoryC::API_URL_GET_ALL, [CategoryC::class, CategoryC::METHOD_GET_ALL]);
        Route::post(CategoryC::API_URL_CREATE_CATEGORY, [CategoryC::class, CategoryC::METHOD_CREATE]);
        Route::post(CategoryC::API_URL_UPDATE_CATEGORY, [CategoryC::class, CategoryC::METHOD_UPDATE]);
    });

    Route::group(['prefix' => ProductC::PREFIX], function () {
        Route::post(ProductC::API_URL_GET_PRODUCTS, [ProductC::class, ProductC::METHOD_GET_PRODUCTS]);
        Route::get(ProductC::API_URL_ADD_TO_CART, [ProductC::class, ProductC::METHOD_ADD_TO_CART]);
        Route::delete(ProductC::API_URL_REMOVE_FROM_CART, [ProductC::class, ProductC::METHOD_REMOVE_FROM_CART]);
        Route::post(ProductC::API_URL_CREATE_PRODUCT, [ProductC::class, ProductC::METHOD_CREATE_PRODUCT]);
        Route::post(ProductC::API_URL_UPDATE_PRODUCT, [ProductC::class, ProductC::METHOD_UPDATE_PRODUCT]);
        Route::delete(ProductC::API_URL_DELETE_PRODUCT, [ProductC::class, ProductC::METHOD_DELETE_PRODUCT]);
        Route::get(ProductC::API_URL_GET_ALL, [ProductC::class, ProductC::METHOD_GET_ALL]);
    });

    Route::group(['prefix' => ProductC::PREFIX], function () {
        Route::post(ProductC::API_URL_GET_PRODUCTS, [ProductC::class, ProductC::METHOD_GET_PRODUCTS]);
        Route::get(ProductC::API_URL_ADD_TO_CART, [ProductC::class, ProductC::METHOD_ADD_TO_CART]);
        Route::delete(ProductC::API_URL_REMOVE_FROM_CART, [ProductC::class, ProductC::METHOD_REMOVE_FROM_CART]);
        Route::post(ProductC::API_URL_CREATE_PRODUCT, [ProductC::class, ProductC::METHOD_CREATE_PRODUCT]);
        Route::post(ProductC::API_URL_UPDATE_PRODUCT, [ProductC::class, ProductC::METHOD_UPDATE_PRODUCT]);
        Route::delete(ProductC::API_URL_DELETE_PRODUCT, [ProductC::class, ProductC::METHOD_DELETE_PRODUCT]);
        Route::get(ProductC::API_URL_GET_ALL, [ProductC::class, ProductC::METHOD_GET_ALL]);
    });

    Route::group(['prefix' => ProductOrderC::PREFIX], function () {
        Route::post(ProductOrderC::API_URL_CHECKOUT, [ProductOrderC::class, ProductOrderC::METHOD_CHECKOUT]);
        Route::get(ProductOrderC::API_URL_GET_ORDER_DETAILS, [ProductOrderC::class, ProductOrderC::METHOD_GET_ORDER_DETAILS]);
        Route::post(ProductOrderC::API_URL_CANCEL_PRODUCT_ORDER, [ProductOrderC::class, ProductOrderC::METHOD_CANCEL_ORDER]);
    });

    Route::group(['prefix' => StoreC::PREFIX], function () {
        Route::get(StoreC::API_URL_GET_STORES, [StoreC::class, StoreC::METHOD_GET_STORES]);
        Route::get(StoreC::API_URL_GET_STORE, [StoreC::class, StoreC::METHOD_GET_STORE]);
        Route::post(StoreC::API_URL_CREATE_STORE, [StoreC::class, StoreC::METHOD_CREATE_STORE]);
        Route::get(StoreC::API_URL_GET_CITIES_HAVE_STORE, [StoreC::class, StoreC::METHOD_GET_CITIES_HAVE_STORE]);
        Route::post(StoreC::API_URL_GET_STORE_BY_CITY, [StoreC::class, StoreC::METHOD_GET_STORE_BY_CITY]);
        Route::post(StoreC::API_URL_UPDATE_STORE, [StoreC::class, StoreC::METHOD_UPDATE_STORE]);
        Route::delete(StoreC::API_URL_DELETE_STORE, [StoreC::class, StoreC::METHOD_DELETE_STORE]);
        Route::post(StoreC::API_URL_UPDATE_WORK_SCHEDULE, [StoreC::class, StoreC::METHOD_UPDATE_WORK_SCHEDULE]);
        Route::get(StoreC::API_URL_GET_BOOKING_TIME, [StoreC::class, StoreC::METHOD_GET_BOOKING_TIME]);
    });

    Route::group(['prefix' => ServiceC::PREFIX], function () {
        Route::post(ServiceC::API_URL_GET_SERVICES, [ServiceC::class, ServiceC::METHOD_GET_SERVICES]);
        Route::post(ServiceC::API_URL_CREATE_SERVICE, [ServiceC::class, ServiceC::METHOD_CREATE_SERVICE]);
        Route::get(ServiceC::API_URL_GET_ALL_SERVICES_WITH_CATEGORY, [ServiceC::class, ServiceC::METHOD_GET_ALL_SERVICES_WITH_CATEGORY]);
        Route::post(ServiceC::API_URL_UPDATE_SERVICE, [ServiceC::class, ServiceC::METHOD_UPDATE_SERVICE]);
        Route::delete(ServiceC::API_URL_DELETE_SERVICE, [ServiceC::class, ServiceC::METHOD_DELETE_SERVICE]);
    });

    Route::group(['prefix' => ServiceCategoryC::PREFIX], function () {
        Route::get(ServiceCategoryC::API_URL_GET_CATEGORIES, [ServiceCategoryC::class, ServiceCategoryC::METHOD_GET_ALL]);
        Route::post(ServiceCategoryC::API_URL_CREATE, [ServiceCategoryC::class, ServiceCategoryC::METHOD_CREATE]);
    });

    Route::group(['prefix' => HomeController::PREFIX], function () {
        Route::get(HomeController::API_URL_GET_DATA, [HomeController::class, HomeController::METHOD_GET_DATA]);
        Route::get(HomeController::API_URL_GET_ALL_CATEGORIES_AND_PRODUCTS, [HomeController::class, HomeController::METHOD_GET_ALL_CATEGORIES_AND_PRODUCTS]);
        Route::get(HomeController::API_URL_GET_ALL_CATEGORIES_AND_SERVICES, [HomeController::class, HomeController::METHOD_GET_ALL_CATEGORIES_AND_SERVICES]);
    });

    Route::group(['prefix' => ServiceOrderC::PREFIX], function () {
        Route::post(ServiceOrderC::API_URL_ORDER, [ServiceOrderC::class, ServiceOrderC::METHOD_ORDER]);
        Route::get(ServiceOrderC::API_URL_GET_ORDER_DETAILS, [ServiceOrderC::class, ServiceOrderC::METHOD_GET_ORDER_DETAILS]);
        Route::post(ServiceOrderC::API_URL_CANCEL_ORDER, [ServiceOrderC::class, ServiceOrderC::METHOD_CANCEL_ORDER]);
        Route::post(ServiceOrderC::API_URL_GIVE_FEEDBACK, [ServiceOrderC::class, ServiceOrderC::METHOD_GIVE_FEEDBACK]);
    });

    Route::group(['prefix' => ManagerC::PREFIX, 'middleware' => 'role:' . RoleEnum::MANAGER->value], function () {

        Route::group(['prefix' => 'staff'], function () {
            Route::post('/', [ManagerC::class, 'createStaff']);
            Route::get('/', [ManagerC::class, 'getAllStaffs']);
            Route::post('/{staffId}', [ManagerC::class, 'updateStaff']);
            Route::delete('/{staffId}', [ManagerC::class, 'deleteStaff']);
            Route::get('/{staffId}', [ManagerC::class, 'getStaffById']);
        });

        Route::group(['prefix' => 'shift'], function () {
            Route::post(ManagerC::API_URL_CREATE_SHIFT, [ManagerC::class, ManagerC::METHOD_CREATE_SHIFT]);
            Route::get(ManagerC::API_URL_GET_ALL_SHIFTS, [ManagerC::class, ManagerC::METHOD_GET_ALL_SHIFTS]);
            Route::post(ManagerC::API_URL_UPDATE_SHIFT, [ManagerC::class, ManagerC::METHOD_UPDATE_SHIFT]);
            Route::delete(ManagerC::API_URL_DELETE_SHIFT, [ManagerC::class, ManagerC::METHOD_DELETE_SHIFT]);
        });

        Route::group(['prefix' => 'service-order'], function () {
            Route::post(ManagerC::API_URL_GET_SERVICE_ORDER, [ManagerC::class, ManagerC::METHOD_FILTER_SERVICE_ORDER]);
            Route::get(ManagerC::API_URL_CONFIRM_SERVICE_ORDER, [ManagerC::class, ManagerC::METHOD_CONFIRM_SERVICE_ORDER]);
            Route::post(ManagerC::API_URL_CANCEL_SERVICE_ORDER, [ManagerC::class, ManagerC::METHOD_CANCEL_SERVICE_ORDER]);
            Route::get(ManagerC::API_URL_MARK_COMPLETE_SERVICE_ORDER, [ManagerC::class, ManagerC::METHOD_MARK_COMPLETE_SERVICE_ORDER]);
        });

        Route::group(['prefix' => 'product-order'], function () {
            Route::post(ManagerC::API_URL_GET_PRODUCT_ORDER, [ManagerC::class, ManagerC::METHOD_FILTER_PRODUCT_ORDER]);
            Route::post(ManagerC::API_URL_CANCEL_PRODUCT_ORDER, [ManagerC::class, ManagerC::METHOD_CANCEL_PRODUCT_ORDER]);
            Route::get(ManagerC::API_URL_CONFIRM_PRODUCT_ORDER, [ManagerC::class, ManagerC::METHOD_CONFIRM_PRODUCT_ORDER]);
        });

        Route::group(['prefix' => 'skill'], function () {
            Route::get(ManagerC::API_URL_GET_ALL_SKILLS, [ManagerC::class, ManagerC::METHOD_GET_ALL_SKILLS]);
        });
    });
});


Route::get('/notification', function () {
    $fields['include_player_ids'] = [env('ONE_SIGNAL_APP_ID')];
    $message = 'hey!! This is a test push.!';
    $result = OneSignal::sendPush($fields, $message);
    return response()->json($result);
});
