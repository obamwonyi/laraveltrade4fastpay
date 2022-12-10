<?php


use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EmailVerificationController;
use App\Http\Controllers\Api\V1\TradeController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


//public routes 


//grouping if for api versioning 
Route::group(
    [
        "prefix" => "/V1",
    ], 

    function () 
    {
        Route::post('login', [AuthController::class,'login']);
        Route::post('register', [AuthController::class,'register']);
        Route::get('send-verify-mail/{email}',[EmailVerificationController::class,'sendVerifyMail']);
        //Route::get('verify-mail/{token}',[EmailVerificationController::class,'verifyMail']);
        Route::get('forgot-password/{email}',[AuthController::class, 'forgotPassword']);
        Route::post('reset-password/{token}',[AuthController::class,'resetPassword']);



        //protected routes
        //add the verified to the middleware
        Route::group([
            'middleware' => ['auth:sanctum']
        ],
        function () 
        {
            Route::resource('trade',TradeController::class);
            Route::post('logout',[AuthController::class, 'logout']);
        }
        );
    }
    );

