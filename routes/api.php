<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CampaignController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(
    [
        'prefix' => 'v1/auth',
        'name' => 'auth.'
    ],
    function() {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::get('/verify/{token}', [AuthController::class, 'verify'])->name('verify');
        Route::post('/check-email', [AuthController::class, 'checkEmail'])->name('checkEmail');
        Route::post('/check-token', [AuthController::class, 'checkToken'])->name('checkToken');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('resetPassword');
    }
);

Route::group(
    [
        'middleware' => 'auth:sanctum',
        'prefix' => 'v1/user',
        'name' => 'user.'
    ],
    function() {
        Route::get('/', [UserController::class, 'show'])->name('show');
        Route::patch('/', [UserController::class, 'update'])->name('update');
    }
);

Route::group(
    [
        'middleware' => 'auth:sanctum',
        'prefix' => 'v1/campaigns',
        'name' => 'campaign.'
    ],
    function() {
        Route::get('/', [CampaignController::class, 'index'])->name('index');
        Route::post('/', [CampaignController::class, 'store'])->name('store');
        Route::get('/{id}', [CampaignController::class, 'show'])->name('show');
        Route::patch('/{id}', [CampaignController::class, 'update'])->name('update');
    }
);

