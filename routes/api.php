<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;
use App\Http\Controllers\GitHubController;

Route::post('/ai/analyze', [AIController::class, 'analyze'])
    ->name('ai.analyze');

Route::post('/github/issues', [GitHubController::class, 'getIssues'])
    ->name('github.issues');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});