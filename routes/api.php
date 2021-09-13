<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UserWalletController;
use App\Http\Controllers\UsersController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('wallettype')->group(function () {
    Route::get('type',  [WalletsController::class, 'index']);
    Route::get('type/{id}', [WalletsController::class, 'getSingleWallettype']);
    Route::post('type/create',  [WalletsController::class, 'createWallettype']);
    Route::put('type/update/{id}', [WalletsController::class, 'updateWallettype']);
    Route::delete('type/delete/{id}',[WalletsController::class, 'deleteWallettype']);
    Route::get('/test/{id}',[WalletsController::class, 'generate_wallet']);
   
});

Route::prefix('wallets')->group(function () {
    Route::get('allwallet',  [UserWalletController::class, 'index']);
    Route::get('singlewallet/{id}', [UserWalletController::class, 'getSingleWalletdetail']);   
});

Route::prefix('users')->group(function () {
    Route::get('alluser',  [UsersController::class, 'index']);
    Route::get('singleuser/{id}', [UsersController::class, 'getSingleUserdetail']);
    Route::post('create',  [UsersController::class, 'createuser']);
    Route::post('createwallet',[UserWalletController::class, 'createwallet']);
});

Route::prefix('counts')->group(function () {
    Route::get('allcounts',  [UsersController::class, 'index']);
    Route::get('usercount', [UsersController::class, 'getUserCount']);
    Route::get('walletcount',  [UserWalletController::class, 'getWaletCount']);
    Route::get('totalwalletsbalance', [UserWalletController::class, 'getAllWaletBalance']);
    Route::get('totaltransactions',[TransactionsController::class, 'gettotaltransactionvolume']);
});

Route::prefix('transactions')->group(function () {
    Route::post('sendmoney',  [TransactionsController::class, 'sendmoney']);
    Route::post('receivemoney',  [TransactionsController::class, 'receivemoney']);
    Route::post('test',  [TransactionsController::class, 'debit_wallet']);
});
