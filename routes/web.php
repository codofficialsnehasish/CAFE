<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    Sliders,
    CategoryController,
    ProductController,
    Customers,
    CouponController,
    OrderController,
};

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function(){
    Route::prefix('admin')->group( function() {
        Route::resource('customers', Customers::class);
        Route::resource('slider', Sliders::class);
        Route::resource('category', CategoryController::class);

        Route::controller(ProductController::class)->group( function () {
            Route::prefix('product')->group( function () {
                Route::get('','index')->name('product.index');
                Route::post('get-products-by-category','get_products_by_category_id')->name('products.get-products-by-category');
                Route::post('update-product-stock','update_product_stock')->name('products.update-product-stock');
                Route::get('basic-info-create','basic_info_create')->name('products.basic-info-create');
                Route::post('basic-info-process','basic_info_process')->name('products.add-basic-info');

                Route::get('basic-info-edit/{id?}','basic_info_edit')->name('products.basic-info-edit');
                Route::post('basic-info-edit-process','basic_info_edit_process')->name('products.add-basic-edit-info');

                Route::get('price-edit/{id?}','price_edit')->name('products.price-edit');
                Route::post('price-edit-process','price_edit_process')->name('products.price-edit-process');

                
                Route::get('inventory-edit/{id?}','inventory_edit')->name('products.inventory-edit');
                Route::post('inventory-edit-process','inventory_edit_process')->name('products.inventory-edit-process');
                
                Route::get('variation-edit/{id?}','variation_edit')->name('products.variation-edit');
                Route::post('variation-edit-process','variation_edit_process')->name('products.variation-edit-process');
                
                Route::get('product-images-edit/{id?}','product_images_edit')->name('products.product-images-edit');
                Route::post('product-gallery-save','productGalleryStore')->name('products.product-gallery-save');
                Route::post('get-product-temp-images','productTempImages')->name('products.get-product-temp-images');
                Route::post('delete-product-images','delete_product_media')->name('products.delete-product-images');
                Route::post('set-main-product-image','set_main_product_image')->name('products.set-main-product-image');
                Route::post('product-images-process','product_images_process')->name('products.product-images-process');


                Route::get('product-addons-edit/{id?}','product_addons_edit')->name('products.product-addons-edit');
                Route::post('product-addons-update','product_addons_update')->name('products.product-addons-update');

                Route::delete('delete/{id}','destroy')->name('products.delete');
            });
        });

        Route::resource('coupon', CouponController::class);

        Route::controller(OrderController::class)->group( function() {
            Route::prefix('orders')->group( function(){
                Route::get('','index')->name('order.index');
                Route::get('{id}/details','show')->name('order.details');
                Route::post('update-order-status','update_order_status')->name('order.update-order-status');
                Route::post('update-payment-status','update_payment_status')->name('order.update-payment-status');
                Route::delete('{id}/destroy','destroy')->name('order.destroy');
            });
        });
    });
});

require __DIR__.'/auth.php';
