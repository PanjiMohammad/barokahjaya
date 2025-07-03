<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;

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

// intended route version
// Route::get('/set-intended', function (Request $request) {
//     $url = $request->query('url');

//     // Validasi URL untuk alasan keamanan (opsional tapi disarankan)
//     if (!Str::startsWith($url, config('app.url'))) {
//         abort(403, 'Invalid redirect URL');
//     }

//     session(['url.intended' => $url]);

//     return response()->json(['message' => 'Intended URL saved']);
// });

// front index
Route::get('/', 'Ecommerce\FrontController@index')->name('front.index');

// for member see produk
Route::get('/product', 'Ecommerce\FrontController@product')->name('front.product');
Route::get('/category/{slug}', 'Ecommerce\FrontController@categoryProduct')->name('front.category');
Route::get('/product/{slug}', 'Ecommerce\FrontController@show')->name('front.show_product');

// for member cart
Route::post('cart', 'Ecommerce\CartController@addToCart')->name('front.cart');
Route::get('/cart', 'Ecommerce\CartController@listCart')->name('front.list_cart');
Route::get('/cart/delete', 'Ecommerce\CartController@deleteCart')->name('front.delete_cart');
Route::post('/cart/update', 'Ecommerce\CartController@updateCart')->name('front.update_cart');

// checkout
Route::get('/checkout', 'Ecommerce\CartController@checkout')->name('front.checkout');
Route::post('/checkout', 'Ecommerce\CartController@processCheckout')->name('front.store_checkout');
Route::get('/checkout/{invoice}', 'Ecommerce\CartController@checkoutFinish')->name('front.finish_checkout');

// Login
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@postLogin')->name('post.newLogin');

// Register
Route::get('/register', 'Auth\LoginController@newRegister')->name('register');
Route::post('/register', 'Auth\LoginController@postRegister')->name('post.newRegister');

// Forgot Password
Route::get('/forgot-password', 'Auth\ResetPasswordController@forgotPasswordForm')->name('forgotPassword');
Route::post('/forgot-password', 'Auth\ResetPasswordController@sendPasswordResetLink')->name('sendPasswordResetLink');
Route::post('/reset-password-users', 'Auth\LoginController@resetPasswordUser')->name('resetPasswordUser');

// Logout
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// // Auth::routes();
// Route::match(['get', 'post'], '/register', function () {
//     return redirect('/login');
// })->name('register');

// for Administrator
Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/setting-acount/{id}', 'HomeController@settingAcount')->name('setting.acount');
    Route::put('/update-acount', 'HomeController@updateAcount')->name('setting.updateAcount');

    // Kategori
    Route::get('/category', 'CategoryController@index')->name('category.index');
    Route::get('/category/getDatatables', 'CategoryController@getDatatables')->name('category.getDatatables');
    Route::get('/category/add-category', 'CategoryController@create')->name('category.create');
    Route::post('/category/store-category', 'CategoryController@store')->name('category.store');
    Route::delete('/category/delete-category/{id}', 'CategoryController@destroy')->name('category.destroy');
    Route::get('/category/edit-category/{id}', 'CategoryController@edit')->name('category.edit');
    Route::put('/category/update-category/{id}', 'CategoryController@update')->name('category.update');

    // Produk
    Route::get('/product', 'ProductController@index')->name('product.newIndex');
    Route::get('/product/datatables', 'ProductController@datatables')->name('product.datatables');
    Route::get('/product/add-product', 'ProductController@create')->name('product.newCreate');
    Route::post('/product/store-product', 'ProductController@store')->name('product.newStore');
    Route::delete('/product/delete-product/{id}', 'ProductController@destroy')->name('product.newDestroy');
    Route::get('/product/edit-product/{id}', 'ProductController@edit')->name('product.newEdit');
    Route::put('/product/update-product', 'ProductController@update')->name('product.newUpdate');
    Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.newBulk');
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.newSaveBulk');
    Route::get('/products/{id}', 'ProductController@show')->name('product.newShow');

    // Pesanan
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'OrderController@index')->name('orders.newIndex');
        Route::get('/finish', 'OrderController@orderFinishIndex')->name('orders.finishIndex');
        Route::get('/finishDatatables', 'OrderController@orderFinishDatatables')->name('orders.finishDatatables');
        Route::get('/datatables', 'OrderController@datatables')->name('orders.newDatatables');
        Route::get('/{invoice}', 'OrderController@view')->name('orders.newView');
        // Route::get('/order-cancelled/datatables', 'OrderController@orderCancelDatatables')->name('orders.cancelGetDatatables');
        Route::get('/payment/{invoice}', 'OrderController@acceptPayment')->name('orders.new_approve_payment');
        Route::post('/process', 'OrderController@processOrder')->name('orders.newProcess');
        Route::post('/shipping', 'OrderController@shippingOrder')->name('orders.newShipping');
        Route::get('/return/{invoice}', 'OrderController@return')->name('orders.newReturn');
        Route::get('/return-details/{invoice}/{product_id}', 'OrderController@newReturnDetails')->name('orders.newReturnDetails');
        Route::post('/return', 'OrderController@approveReturn')->name('orders.new_approve_return');
        Route::delete('/{id}', 'OrderController@destroy')->name('orders.newDestroy');
    });

    Route::group(['prefix' => 'reports'], function() {
        Route::match(['get', 'post'], '/', function () {
            return redirect('administrator/reports/order');
        });
        Route::get('/order', 'OrderController@orderReport')->name('report.order');
        Route::get('/order/orderReportDatatables', 'OrderController@getOrderReportDatatables')->name('report.newDatatables');
        Route::get('/reportorder/{daterange}', 'OrderController@orderReportPdf')->name('report.order_pdf');
        Route::get('/return', 'OrderController@returnReport')->name('report.return');
        Route::get('/return/orderReportReturnDatatables', 'OrderController@returnReportDatatables')->name('return.newDatatables');
        Route::get('/reportreturn/{daterange}', 'OrderController@returnReportPdf')->name('report.return_pdf');
    });
});

Route::group(['prefix' => 'member', 'namespace' => 'Ecommerce'], function() {
    Route::match(['get', 'post'], '/', function () {
        return redirect('member/dashboard');
    });

    // login
    Route::get('login', 'LoginController@loginForm')->name('customer.login');
    Route::post('login', 'LoginController@login')->name('customer.post_login');

    // registrasi
    Route::get('register', 'RegisterController@registerForm')->name('customer.register');
    Route::post('register', 'RegisterController@register')->name('customer.post_register');

    // lupa password
    Route::get('forgot-password', 'LoginController@forgotPassword')->name('customer.forgotPassword');
    Route::post('post-forgot-password', 'LoginController@ResetPassword')->name('customer.postForgotPassword');

    // aktivasi token
    Route::get('verify/{token}', 'FrontController@verifyCustomerRegistration')->name('customer.verify');

    Route::group(['middleware' => 'customer'], function() {

        // dashboard
        Route::get('dashboard', 'LoginController@dashboard')->name('customer.dashboard');

        // pesanan
        Route::get('orders', 'OrderController@index')->name('customer.orders');
        Route::get('orders/getDatatables', 'OrderController@getIndexDatatables')->name('customer.orderDatatables');
        Route::get('orders/{invoice}', 'OrderController@view')->name('customer.view_order');

        Route::get('orders/pdf/{invoice}', 'OrderController@pdf')->name('customer.order_pdf');
        Route::post('orders/accept', 'OrderController@acceptOrder')->name('customer.order_accept');
        Route::get('orders/return/{invoice}', 'OrderController@returnForm')->name('customer.order_return');
        Route::put('orders/return', 'OrderController@processReturn')->name('customer.return');

        // payment
        Route::get('payment/{invoice}', 'OrderController@paymentForm')->name('customer.paymentForm');
        Route::post('payment/save', 'OrderController@storePayment')->name('customer.savePayment');

        // setting
        Route::get('setting', 'FrontController@customerSettingForm')->name('customer.settingForm');
        Route::post('setting', 'FrontController@customerUpdateProfile')->name('customer.setting');

        // daftar keinginan
        Route::get('wishlists', 'WishlistController@index')->name('customer.wishlist');
        Route::get('wishlists/getDatatables', 'WishlistController@getWishlistDatatables')->name('customer.wishlistDatatables');
        Route::post('wishlists', 'WishlistController@saveWishlist')->name('customer.save_wishlist');
        Route::delete('wishlists/{id}', 'WishlistController@deleteWishlist')->name('customer.deleteWishlist');

        // logout
        Route::get('logout', 'LoginController@logout')->name('customer.logout');
    });
});
