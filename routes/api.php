<?php

use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\SubtaskApiController;

use Illuminate\Support\Facades\Route;

// User API routes
Route::post('/registration', [UserApiController::class, 'registration']);
Route::post('/login', [UserApiController::class, 'login']);

// Task API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('task')->group(function () {
        Route::get('/get', [TaskApiController::class, 'getMainTasks']);
        Route::post('/create', [TaskApiController::class, 'createMainTask']);
        Route::put('/update/{mainTaskId}', [TaskApiController::class, 'updateMainTask']);
        Route::delete('/delete/{mainTaskId}', [TaskApiController::class, 'deleteMainTask']);
        Route::put('/mark-complete/complete/{mainTaskId}', [TaskApiController::class, 'markMainTaskAsComplete']);
    });

    Route::prefix('subtask')->group(function () {
        Route::get('/get', [SubtaskApiController::class, 'getSubtasks']);
        Route::post('/create/{mainTaskId}', [SubtaskApiController::class, 'createSubtask']);
        Route::put('/update/{mainTaskId}/{subtaskId}', [SubtaskApiController::class, 'updateSubtask']);
        Route::delete('delete/{mainTaskId}/{subtaskId}', [SubtaskApiController::class, 'deleteSubtask']);
        Route::put('/mark-complete/complete/{mainTaskId}/{subtaskId}', [SubtaskApiController::class, 'markSubtaskAsComplete']);
    });

    Route::post('/logout', [UserApiController::class, 'logout']);
});

/**
 * Routes with filters for main tasks
 * http://localhost:8000/api/task/get - get all main tasks
 * http://localhost:8000/api/task/get?status=todo - get all main tasks by filtering them by status todo
 * http://localhost:8000/api/task/get?status=done - get all main tasks by filtering them by status done
 * http://localhost:8000/api/task/get?title=anyletters - get all main tasks by filtering them by title
 * http://localhost:8000/api/task/get?sort_by=created_at&sort_direction=desc - get all main tasks by filtering them by Sort by creation time (descending order)
 * http://localhost:8000/api/task/get?sort_by=completedAt&sort_direction=desc - get all main tasks by filtering them by Sort by completed time (descending order)
 * http://localhost:8000/api/task/get?sort_by=priority&sort_direction=desc - get all main tasks by filtering them by Sort by priority (descending order)
 * http://localhost:8000/api/task/get?priority_from=2&priority_to=4 - get all main tasks by filtering them by priority from 'min priority' to 'max priority'
 *
 * Routes with editing operations for main tasks
 * http://localhost:8000/api/task/create - create a new main task
 * http://localhost:8000/api/task/update/mainTaskId - edit main task by id specific main task
 * http://localhost:8000/api/task/delete/mainTaskId - delete main task by id specific main task
 * http://localhost:8000/api/task/mark-complete/complete/mainTaskId - mark as complete main task by id specific main task
 *
 *
 * Routes with filters for subtasks
 * http://localhost:8000/api/subtask/get - get all subtasks
 * http://localhost:8000/api/subtask/get?status=todo - get all subtasks by filtering them by status todo
 * http://localhost:8000/api/subtask/get?status=done - get all subtasks by filtering them by status done
 * http://localhost:8000/api/subtask/get?title=anyletters - get all subtasks by filtering them by title
 * http://localhost:8000/api/subtask/get?sort_by=created_at&sort_direction=desc - get all subtasks by filtering them by Sort by creation time (descending order)
 * http://localhost:8000/api/subtask/get?sort_by=completedAt&sort_direction=desc - get all subtasks by filtering them by Sort by completed time (descending order)
 * http://localhost:8000/api/subtask/get?sort_by=priority&sort_direction=desc - get all subtasks by filtering them by Sort by priority (descending order)
 * http://localhost:8000/api/subtask/get?priority_from=2&priority_to=4 - get all subtasks by filtering them by priority from 'min priority' to 'max priority'
 *
 * Routes with editing operations for subtasks
 * http://localhost:8000/api/subtask/create/mainTaskId - create a new subtask and use (mainTaskId) whose id is from the specific main task in the table tasks
 * http://localhost:8000/api/subtask/update/mainTaskId/subtaskId - edit subtask by (mainTaskId) and by (subtaskId)
 * http://localhost:8000/api/subtask/delete/mainTaskId/subtaskId - delete subtask by (mainTaskId) and by (subtaskId)
 * http://localhost:8000/api/subtask/mark-complete/complete/mainTaskId/subtaskId - mark as complete subtask by (mainTaskId) and by (subtaskId)
 */
