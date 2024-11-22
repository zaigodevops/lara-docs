<?php
use Illuminate\Support\Facades\Route;
use ZaigoInfotech\LaraDocs\Http\Controllers\CategoryController;
use ZaigoInfotech\LaraDocs\Http\Controllers\DocumentController;
use ZaigoInfotech\LaraDocs\Http\Controllers\DashboardController;
use ZaigoInfotech\LaraDocs\Http\Controllers\AdminController;


/**** ADMIN PROFILE ROUTES ****/
// Route::group(['middleware' => 'auth'], function () {
// // Route::get('admin/', 'App\Http\Controllers\Admin\UserController@dashboard')->name('dashboard');
// });

Route::group(['prefix' => 'admin' ,'middleware' => 'auth'], function () {
    // Route::get('/dashboard', 'App\Http\Controllers\Admin\UserController@dashboard')->name('dashboard');
    // Route::get('profile/edit', [AdminController::class, 'edit'])->name('admin.profile.edit');
    // Route::put('{user}/update', [AdminController::class, 'profile_update'])->name('admin.profile.update');
    
    Route::get('category',[CategoryController::class ,'index'])->name('category.index');
    Route::post('category/create',[CategoryController::class ,'create'])->name('category.create');
    Route::post('category/update/{id}',[CategoryController::class ,'update'])->name('category.update');
    Route::post('category/delete/{id}',[CategoryController::class ,'delete'])->name('category.delete');

    Route::get('document/list',[DocumentController::class ,'list'])->name('document.list');
    Route::get('document',[DocumentController::class ,'index'])->name('document.index');
    Route::post('document/upload',[DocumentController::class ,'upload'])->name('document.upload');
    Route::get('document/view', [DocumentController::class, 'viewdocument'])->name('view.document');
    Route::get('document/download', [DocumentController::class, 'downloaddocument'])->name('download.document');
    Route::get('document/delete', [DocumentController::class, 'deletedocument'])->name('delete.document');
});


/**** USER ROUTES ****/
// Route::group(['middleware' => 'auth'], function () {
// Route::get('user/', [DashBoardController::class, 'index'])->name('user.dashboard');
// });

Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
    // Route::get('dashboard', [DashBoardController::class, 'index'])->name('user.dashboard');
    // Route::get('profile/edit', [DashBoardController::class, 'edit'])->name('user.profile.edit');
    // Route::put('update/{user}', [DashBoardController::class, 'update'])->name('user.profile.update');
    // Route::get('/signin',[DashBoardController::class,'signin'])->name('user.signin');
    // Route::post('/password/save', [DashBoardController::class, 'savepassword'])->name('password.save');
    Route::get('document', [DashBoardController::class, 'document'])->name('user.document');
    Route::get('document/view', [DashBoardController::class, 'viewdocument'])->name('user.view.document');
    Route::get('document/download', [DashBoardController::class, 'downloaddocument'])->name('user.download.document');
});

