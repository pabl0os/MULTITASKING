<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'showLogin']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']); // Fallback convenience

Route::middleware('auth')->group(function () {
    Route::delete('/account', [AuthController::class, 'destroyAccount'])->name('account.delete');

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Projects CRUD & Member Management
    Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects');
    Route::post('/projects', [\App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projects/{project}/members', [\App\Http\Controllers\ProjectController::class, 'addMember'])->name('projects.members.add');
    Route::delete('/projects/{project}/members/{member}', [\App\Http\Controllers\ProjectController::class, 'removeMember'])->name('projects.members.remove');
    Route::put('/projects/{project}/members/{member}/role', [\App\Http\Controllers\ProjectController::class, 'updateMemberRole'])->name('projects.members.role');
    Route::post('/projects/{project}/complete', [\App\Http\Controllers\ProjectController::class, 'completeProject'])->name('projects.complete');

    // Tasks CRUD, Comments & Dependencies
    Route::get('/tasks', [\App\Http\Controllers\TaskController::class, 'index'])->name('tasks');
    Route::post('/tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/dependencies', [\App\Http\Controllers\TaskController::class, 'addDependency'])->name('tasks.dependencies.add');
    Route::delete('/tasks/{task}/dependencies/{dependency}', [\App\Http\Controllers\TaskController::class, 'removeDependency'])->name('tasks.dependencies.remove');
    Route::post('/tasks/{task}/comments', [\App\Http\Controllers\TaskController::class, 'addComment'])->name('tasks.comments.add');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::put('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Translation
    Route::get('/translation', [\App\Http\Controllers\TranslationController::class, 'index'])->name('translation');
    Route::post('/translation/translate', [\App\Http\Controllers\TranslationController::class, 'translate'])->name('translation.translate');

    // Location
    Route::get('/location', [\App\Http\Controllers\LocationController::class, 'index'])->name('location');
});

