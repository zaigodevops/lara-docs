<?php
use Illuminate\Support\Facades\Route;
use ZaigoInfotech\LaraDocs\Http\Controllers\CategoryController;
use ZaigoInfotech\LaraDocs\Http\Controllers\DocumentController;

/**** LARA_DOCS ADMIN ROUTES ****/
Route::group(['prefix' => 'admin' ,'middleware' => 'auth'], function () {
 
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


