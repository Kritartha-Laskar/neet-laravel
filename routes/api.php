<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\QuestionPaperApiController;
use App\Http\Controllers\Api\ResourceApiController;
use App\Http\Controllers\Api\ClassApiController;

use App\Http\Controllers\Api\ExamApiController;

/*
|--------------------------------------------------------------------------
| API Routes  —  prefix: /api
|--------------------------------------------------------------------------
| All routes here are stateless (no sessions).
| Protected routes require:  Authorization: Bearer <token>
|--------------------------------------------------------------------------
*/

// ── Public routes (no token needed) ───────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthApiController::class, 'register']);   // POST /api/auth/register
    Route::post('login',    [AuthApiController::class, 'login']);      // POST /api/auth/login
});

// ── Protected routes (Custom API token required) ─────────────────────────────
Route::middleware('auth.api')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthApiController::class, 'logout']);  // POST /api/auth/logout
    Route::get('auth/me',      [AuthApiController::class, 'me']);      // GET  /api/auth/me
    Route::post('auth/change-password', [AuthApiController::class, 'changePassword']); // POST /api/auth/change-password

    // Question Papers
    Route::get('question-papers',      [QuestionPaperApiController::class, 'index']);  // GET  /api/question-papers
    Route::get('question-papers/{questionPaper}', [QuestionPaperApiController::class, 'show']);   // GET  /api/question-papers/{id}

    // Exam Attempt & Real-Time Answer Storage
    Route::post('exam/start',            [ExamApiController::class, 'startExam']);               // POST /api/exam/start
    Route::post('exam/save-answer',      [ExamApiController::class, 'saveAnswer']);              // POST /api/exam/save-answer
    Route::post('exam/submit',           [ExamApiController::class, 'submitExam']);              // POST /api/exam/submit
    Route::get('exam/analytics',         [ExamApiController::class, 'getPerformanceAnalytics']); // GET  /api/exam/analytics
    Route::get('exam/attempt/{attempt}', [ExamApiController::class, 'getAttemptDetail']);        // GET  /api/exam/attempt/{id}

    // Media & Document Resources (Videos, PDFs, Images)
    Route::get('resources',             [ResourceApiController::class, 'index']);      // GET  /api/resources?type=video|pdf|image
    Route::get('resources/type/{type}', [ResourceApiController::class, 'byType']);     // GET  /api/resources/type/videos|pdfs|images
    Route::get('resources/{resource}',  [ResourceApiController::class, 'show']);       // GET  /api/resources/{id}

    // Study Classes (e.g. Class One, Class Two)
    Route::get('classes',               [ClassApiController::class, 'index']);         // GET  /api/classes
    Route::get('classes/{studyClass}',  [ClassApiController::class, 'show']);          // GET  /api/classes/{id}
});
