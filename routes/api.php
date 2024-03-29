<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DUController;
use App\Http\Controllers\Api\Family;
use App\Http\Controllers\Api\Post;
use App\Http\Controllers\Api\Siswa;
use App\Http\Controllers\Api\SPPController;
use App\Http\Controllers\Api\StudyYear;
use App\Http\Controllers\Api\UserAdmin;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources([
    'posts' => Post::class,
    'students' => Siswa::class,
    'study-year' => StudyYear::class,
    'spp' => SPPController::class,
    'family' => Family::class,
    'daftar-ulang' => DUController::class,
    'user-admin' => UserAdmin::class
]);

Route::controller(SPPController::class)->group(function () {
    Route::post('/students/{nisn}/transaction/spp/{study_year}', 'paid_spp_transaction');
    Route::put('/students/{nisn}/transaction/spp/{study_year}/{id_spp}', 'update_spp_transaction');
    Route::delete('/students/{nisn}/transaction/spp/{study_year}/{id_spp}', 'delete_spp_transaction');
    Route::get('/count/spp', 'count_spp');
});

Route::controller(Siswa::class)->group(function () {
    Route::get('/students/{nisn}/transaction/spp', 'check_spp_transaction');
    Route::get('/students/{nisn}/transaction/du', 'check_du_transaction');
    Route::put('/students/update/kelas', 'update_students_class_year');
});
Route::controller(DUController::class)->group(function () {
    Route::post('/students/{nisn}/transaction/du/{study_year}', 'paid_du_transaction');
    Route::put('/students/{nisn}/transaction/du/{study_year}/{id_du}', 'update_du_transaction');
    Route::delete('/students/{nisn}/transaction/du/{study_year}/{id_du}', 'delete_du_transaction');
    Route::get('/count/du', 'count_du');
});

Route::post('/auth-student', [AuthController::class, 'auth']);
Route::post('/auth-admin', [AuthController::class, 'authAdmin']);
Route::post('/register-admin', [AuthController::class, 'register_admin']);
