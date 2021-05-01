<?php

use App\Http\Controllers\AdminApi\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailListController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\CallApi\CleaningController;
use Illuminate\Support\Facades\Route;

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

Route::name('api.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/signup', [AuthController::class, 'register'])->name('register');
    Route::post('/forgot-password', [AuthController::class, 'forgot_password'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'reset_password'])->name('password.update');

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('/verify/{id}/{hash}', [AuthController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
        Route::get('/resend', [AuthController::class, 'resend'])->name('verification.resend');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::group(['middleware' => ['verified']], function () {
            Route::put('/user/chhange-password', [AuthController::class, 'user_change_password'])->name('users.change.password');

            Route::apiResources([
                'lists' => EmailListController::class,
                'plans' => PlanController::class,
            ]);
        });
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('/sanctum/token', [\App\Http\Controllers\AdminApi\AuthController::class, 'create_token'])
        ->name('create.token')
        ->middleware(['IPIsValid', 'throttle:api_admin']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::apiResources([
            'users' => UserController::class,
            'plans' => \App\Http\Controllers\AdminApi\PlanController::class,
        ]);
    });
});
