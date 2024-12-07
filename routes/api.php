<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\{
    AuthenticationController,
    SliderAPI,
    CategoeyAPI,
    ProductAPI,
    CartAPI,
    CouponsAPI,
    OrderAPI,
    AddressAPI,
};


Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/update-profile', [AuthenticationController::class, 'update_profile']);
    Route::get('/get-user-data', [AuthenticationController::class, 'get_user_data']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    Route::get('sliders', [SliderAPI::class, 'index']);

    Route::get('get-categories/{id?}', [CategoeyAPI::class, 'index']);

    Route::controller(ProductAPI::class)->group( function () {
        Route::get('get-products/{id?}','index');
        Route::get('get-products-by-category/{id}','get_products_by_category');
    });

    Route::controller(CartAPI::class)->group( function() {
        Route::post('add-to-cart','add_to_cart');
        Route::get('cart-items','cart_items');
        Route::post('update-cart-quantity','increment_decrement_cart_quantity');
        Route::post('delete-cart-item','delete_cart_item');
        Route::post('clear-cart','clear_cart');
    });

    Route::get('get-coupons', [CouponsAPI::class, 'index']);

    Route::controller(OrderAPI::class)->group( function() {
        Route::post('place-order','place_order');

        Route::get('order-history','order_history');
        Route::get('order-details/{id?}','order_details');
        Route::post('cancel-order','cancel_order');
    });

    Route::controller(AddressAPI::class)->group( function() {
        Route::post('save-address','save_address');
        Route::post('save-as-default-address','save_as_default_address');
        Route::get('get-saved-address','get_saved_address');
    });
});