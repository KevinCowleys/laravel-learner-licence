<?php

use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('assessment-selection');
})->name('home');

Route::group(['prefix' => 'assessment'], function () {
    Route::get('/{testType}/{testNumber}/', [AssessmentController::class, 'createAssessment'])->where(['testType' => '[0-9]', 'testNumber' => '[0-9]', 'assessmentId' => '[0-9]+'])->name('createAssessment');
    Route::get('/{testType}/{testNumber}/{assessmentId}/', [AssessmentController::class, 'loadAssessment'])->where(['testType' => '[0-9]', 'testNumber' => '[0-9]', 'assessmentId' => '[0-9]+'])->name('loadAssessment');
    Route::get('/{testType}/{testNumber}/{assessmentId}/results', [AssessmentController::class, 'loadAssessmentResult'])->where(['testType' => '[0-9]', 'testNumber' => '[0-9]', 'assessmentId' => '[0-9]+'])->name('loadAssessmentResult');
    Route::get('/{testType}/{testNumber}/{assessmentId}/incorrect_answers', [AssessmentController::class, 'loadIncorrectAnswers'])->where(['testType' => '[0-9]', 'testNumber' => '[0-9]', 'assessmentId' => '[0-9]+'])->name('loadIncorrectAnswers');
});

Route::get('/assessments', [AssessmentController::class, 'renderAssessments'])->name('assessments');
