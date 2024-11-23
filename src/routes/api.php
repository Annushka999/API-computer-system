<?php

use App\Http\Controllers\AuthController;
use App\Http\Requests\FileController;
use Illuminate\Support\Facades\Route;


Route::post('/authorization', [AuthController::class, 'login'])->name('login');
Route::post('/registration', [AuthController::class, 'registration']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/shared', [FileController::class])->name('shared.files');
    Route::group(['prefix' => 'files'], function () {
        Route::get('/disk', [FileController::class, 'getFiles'])->name('file.user');
        Route::get('/shared', [FileController::class, 'getSharedFiles'])->name('file.shared');
        Route::post('/', [FileController::class, 'upload'])->name('file.upload');
        Route::get('/{file:file_id}', [FileController::class, 'download'])->name('file.download');

        Route::patch('/{file:file_id}', [FileController::class, 'update'])
            ->middleware('checkOwnerFiles')
            ->name('file.change');

        Route::delete('/{file:file_id}', [FileController::class, 'destroy'])
            ->middleware('checkOwnerFiles')
            ->name('file.delete');

        Route::post('/{file:file_id}/accesses', [FileController::class, 'addAccess'])
            ->middleware('checkOwnerFiles')
            ->name('file.access.create');

        Route::delete('/{file:file_id}/accesses', [FileController::class, 'deleteAccess'])
            ->middleware('checkOwnerFiles')
            ->name('file.access.delete');
    });
});
