<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TokenController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Routing\Route as RoutingRoute;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register', [RegisterController::class, 'register']);
Route::post('/sanctum/token', [tokenController::class, 'createToken']);


Route::middleware(['auth:sanctum', 'blocked'])->group(function () {
    /******************************* Address **************************************** */
    Route::apiResource('addresses', AddressController::class);

    /******************************* Cart **************************************** */

    //create cart api
    Route::post('/carts', [CartController::class, 'newCart']);
    //add cart item api
    Route::post('/carts/items', [CartController::class, 'addCartItem']);
    //display single cart api for user
    Route::get('/carts', [CartController::class, 'getCartDetails']);
    //delete cart item api
    Route::delete('/carts/items/{id}', [CartController::class, 'deleteCartItem']);
    //update cart item quantity api
    Route::put('/carts/items/{id}', [CartController::class, 'updateCartItem']);
    //checkout cart api
    Route::post('/carts/checkout', [CartController::class, 'checkout']);

    /******************************* User **************************************** */


    Route::apiResource('users', UserController::class);



    /******************************* Product **************************************** */

    //display all products api
    Route::get('/products', [ProductController::class, 'index']);
    //display single product api
    Route::get('/products/{id}', [ProductController::class, 'show']);
    //filter products api
    Route::get('/products/filter', [ProductController::class, 'filterProducts']);




    /******************************* Order **************************************** */


    Route::get('/orders/{id}', [OrderController::class, 'show']);
    //show order details api
    Route::get('/orders/{id}/details', [OrderController::class, 'showOrderDetails']);
    //update order Status api
    Route::put('/orders/{order}/update-status/{newStatus}', [OrderController::class, 'updateOrderStatus']);
    //filter orders api
    Route::get('/orders/filter', [OrderController::class, 'filterOrders']);
});


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::put('/users/block/{id}', [UserController::class, 'block']);
    Route::get('/carts/items', [CartController::class, 'listProductsInCart']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('/orders', [OrderController::class, 'index']);
});
