<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// Dashboard (protected)
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::post('/dashboard/classes', [\App\Http\Controllers\DashboardController::class, 'storeClass'])->middleware('auth')->name('dashboard.classes.store');
Route::put('/dashboard/classes/{studyClass}', [\App\Http\Controllers\DashboardController::class, 'updateClass'])->middleware('auth')->name('dashboard.classes.update');
Route::delete('/dashboard/classes/{studyClass}', [\App\Http\Controllers\DashboardController::class, 'destroyClass'])->middleware('auth')->name('dashboard.classes.destroy');
Route::post('/dashboard/resources/assign', [\App\Http\Controllers\DashboardController::class, 'assignResource'])->middleware('auth')->name('dashboard.resources.assign');
Route::post('/dashboard/resources/upload-and-assign', [\App\Http\Controllers\DashboardController::class, 'uploadAndAssignResource'])->middleware('auth')->name('dashboard.resources.upload_assign');
Route::post('/dashboard/resources/{resource}/remove', [\App\Http\Controllers\DashboardController::class, 'removeResource'])->middleware('auth')->name('dashboard.resources.remove');
Route::post('/dashboard/resources/reorder', [\App\Http\Controllers\DashboardController::class, 'reorderResources'])->middleware('auth')->name('dashboard.resources.reorder');

// Admin CRUD routes (protected)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('classes',         \App\Http\Controllers\admin\ClassController::class);
    Route::post('classes/reorder',     [\App\Http\Controllers\admin\ClassController::class, 'reorder'])->name('classes.reorder');
    Route::post('classes/store-question', [\App\Http\Controllers\admin\ClassController::class, 'storeQuestion'])->name('classes.store-question');
    Route::delete('classes/questions/{question}', [\App\Http\Controllers\admin\ClassController::class, 'destroyQuestion'])->name('classes.destroy-question');
    Route::post('classes/assign-resource', [\App\Http\Controllers\admin\ClassController::class, 'assignResource'])->name('classes.assign-resource');
    Route::post('classes/upload-resource', [\App\Http\Controllers\admin\ClassController::class, 'uploadResource'])->name('classes.upload-resource');
    Route::post('classes/remove-resource/{resource}', [\App\Http\Controllers\admin\ClassController::class, 'removeResource'])->name('classes.remove-resource');

    Route::resource('courses',         \App\Http\Controllers\admin\CourseController::class);
    Route::resource('subjects',        \App\Http\Controllers\admin\SubjectController::class);
    Route::resource('questions',       \App\Http\Controllers\admin\QuestionController::class);
    Route::resource('answers',         \App\Http\Controllers\admin\AnswerController::class);
    Route::resource('question-papers', \App\Http\Controllers\admin\QuestionPaperController::class)
         ->only(['index', 'show', 'store']);
    Route::resource('resources',       \App\Http\Controllers\admin\ResourceController::class)->except(['edit', 'update', 'show']);
    Route::post('resources/{resource}/toggle-status', [\App\Http\Controllers\admin\ResourceController::class, 'toggleStatus'])->name('resources.toggle-status');
});

Route::get('/storage/{path}', function ($path) {
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    
    if (!$disk->exists($path)) {
        abort(404);
    }
    
    $fullPath = $disk->path($path);
    
    $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($fullPath);
    \Symfony\Component\HttpFoundation\BinaryFileResponse::trustForProxy();
    
    return $response;
})->where('path', '.*');


