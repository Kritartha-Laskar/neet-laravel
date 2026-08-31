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

Route::middleware(['auth', 'check.role'])->group(function () {
    // Dashboard (protected)
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/classes', [\App\Http\Controllers\DashboardController::class, 'storeClass'])->name('dashboard.classes.store');
    Route::put('/dashboard/classes/{studyClass}', [\App\Http\Controllers\DashboardController::class, 'updateClass'])->name('dashboard.classes.update');
    Route::delete('/dashboard/classes/{studyClass}', [\App\Http\Controllers\DashboardController::class, 'destroyClass'])->name('dashboard.classes.destroy');
    Route::post('/dashboard/resources/assign', [\App\Http\Controllers\DashboardController::class, 'assignResource'])->name('dashboard.resources.assign');
    Route::post('/dashboard/resources/upload-and-assign', [\App\Http\Controllers\DashboardController::class, 'uploadAndAssignResource'])->name('dashboard.resources.upload_assign');
    Route::post('/dashboard/resources/upload-chunk', [\App\Http\Controllers\DashboardController::class, 'uploadChunk'])->name('dashboard.resources.upload_chunk');
    Route::post('/dashboard/resources/{resource}/remove', [\App\Http\Controllers\DashboardController::class, 'removeResource'])->name('dashboard.resources.remove');
    Route::post('/dashboard/resources/reorder', [\App\Http\Controllers\DashboardController::class, 'reorderResources'])->name('dashboard.resources.reorder');

    // Admin CRUD routes (protected)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('classes',         \App\Http\Controllers\admin\ClassController::class);
        Route::post('classes/reorder',     [\App\Http\Controllers\admin\ClassController::class, 'reorder'])->name('classes.reorder');
        Route::post('classes/store-question', [\App\Http\Controllers\admin\ClassController::class, 'storeQuestion'])->name('classes.store-question');
        Route::delete('classes/questions/{question}', [\App\Http\Controllers\admin\ClassController::class, 'destroyQuestion'])->name('classes.destroy-question');
        Route::post('classes/assign-resource', [\App\Http\Controllers\admin\ClassController::class, 'assignResource'])->name('classes.assign-resource');
        Route::post('classes/upload-resource', [\App\Http\Controllers\admin\ClassController::class, 'uploadResource'])->name('classes.upload-resource');
        Route::post('classes/remove-resource/{resource}', [\App\Http\Controllers\admin\ClassController::class, 'removeResource'])->name('classes.remove-resource');

        Route::resource('courses',         \App\Http\Controllers\admin\CourseController::class);
        Route::resource('subjects',        \App\Http\Controllers\admin\SubjectController::class);
        Route::resource('chapters',        \App\Http\Controllers\admin\ChapterController::class);
        Route::get('chapters/by-subject/{subjectId}', [\App\Http\Controllers\admin\ChapterController::class, 'bySubject'])->name('chapters.by-subject');
        Route::resource('questions',       \App\Http\Controllers\admin\QuestionController::class);
        Route::resource('answers',         \App\Http\Controllers\admin\AnswerController::class);
        Route::resource('question-papers', \App\Http\Controllers\admin\QuestionPaperController::class)
             ->only(['index', 'show', 'store', 'destroy']);
        Route::post('question-papers/{questionPaper}/add-questions', [\App\Http\Controllers\admin\QuestionPaperController::class, 'addQuestions'])->name('question-papers.add-questions');
        Route::delete('question-papers/{questionPaper}/questions/{question}', [\App\Http\Controllers\admin\QuestionPaperController::class, 'removeQuestion'])->name('question-papers.remove-question');
        Route::resource('resources',       \App\Http\Controllers\admin\ResourceController::class)->except(['edit', 'update', 'show']);
        Route::post('resources/{resource}/toggle-status', [\App\Http\Controllers\admin\ResourceController::class, 'toggleStatus'])->name('resources.toggle-status');
    });

    // Super Admin only routes
    Route::middleware('check.super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users/create', [\App\Http\Controllers\admin\UserController::class, 'create'])->name('users.create');
        Route::post('users/store', [\App\Http\Controllers\admin\UserController::class, 'store'])->name('users.store');
    });
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

Route::get('/run-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Storage link created successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/resources/{resource}/view', [\App\Http\Controllers\DashboardController::class, 'viewResource'])->name('dashboard.resources.view');
Route::get('/resources/{resource}/thumbnail', [\App\Http\Controllers\DashboardController::class, 'viewThumbnail'])->name('dashboard.resources.thumbnail');
Route::get('/questions/{question}/image', [\App\Http\Controllers\DashboardController::class, 'viewQuestionImage'])->name('questions.image');


