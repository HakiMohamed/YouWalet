<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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


Route::get('/login',[AuthController::class, 'getlogin'])->name('login');

Route::middleware('auth:sanctum')->get('/getauthuser', function(Request $request){
return $request->user();
});
    


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class,'store']); 
    Route::get('/', [UserController::class,'index']); 
    Route::get('/{id}', [UserController::class,'show']); 
    Route::put('/{id}', [UserController::class,'update']); 
    Route::delete('/{id}', [UserController::class,'destroy']); 
});

Route::prefix('roles')->group(function () {
    Route::post('/', [RoleController::class,'store']); 
    Route::get('/', [RoleController::class,'index']); 
    Route::get('/{id}', [RoleController::class,'show']); 
    Route::put('/{id}', [RoleController::class,'update']); 
    Route::delete('/{id}', [RoleController::class,'destroy']); 
});

Route::middleware('auth:sanctum')->prefix('transactions')->group(function () {
    Route::post('/', [TransactionController::class, 'store']);
    Route::get('/user-transactions', [TransactionController::class, 'showUserTransactions']);
    Route::get('/', [TransactionController::class, 'index']);
    Route::get('/{id}', [TransactionController::class, 'show']);
    Route::put('/{id}', [TransactionController::class, 'update']);
    Route::delete('/{id}', [TransactionController::class, 'destroy']);
});

