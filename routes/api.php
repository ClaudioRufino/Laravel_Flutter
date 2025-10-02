<?php

use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/autenticate', [AuthController::class, 'login']);

Route::post('/registar', [AuthController::class, 'registar']);

Route::middleware('auth:sanctum')->put('/updateRegister', [AuthController::class, 'updateRegister']);
Route::middleware('auth:sanctum')->get('/getPersonalData', [AuthController::class, 'getPersonalData']);
Route::middleware('auth:sanctum')->get('/getAcademicData', [AuthController::class, 'getAcademicData']);
Route::middleware('auth:sanctum')->get('/getInscriptionData', [AuthController::class, 'getInscriptionData']);
Route::middleware('auth:sanctum')->get('/getPaymentData', [AuthController::class, 'getPaymentData']);

Route::middleware('auth:sanctum')->get('/getName', [AuthController::class, 'getName']);

// Arquivos
Route::middleware('auth:sanctum')->post('/upload', [AuthController::class, 'upload']);
Route::middleware('auth:sanctum')->post('/uploadComprovativo', [AuthController::class, 'uploadComprovativo']);


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);



Route::get('/teste', [AuthController::class, 'teste']);