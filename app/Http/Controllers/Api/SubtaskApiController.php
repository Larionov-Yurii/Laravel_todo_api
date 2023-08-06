<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\SubTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubtaskApiController extends Controller
{
    public function getSubtasks(Request $request)
    {
        $subtasksQuery = SubTask::query();

        $status        = $request->query('status');
        $title         = $request->query('title');
        $sortBy        = $request->query('sort_by');
        $sortDirection = $request->query('sort_direction');
        $priorityFrom  = $request->query('priority_from');
        $priorityTo    = $request->query('priority_to');

        if ($status) {
            $subtasksQuery->where('status', $status);
        }

        if ($title) {
            $subtasksQuery->where('title', 'like', '%' . $title . '%');
        }

        if ($priorityFrom) {
            $subtasksQuery->where('priority', '>=', $priorityFrom);
        }

        if ($priorityTo) {
            $subtasksQuery->where('priority', '<=', $priorityTo);
        }

        if ($sortBy && in_array($sortBy, ['created_at', 'completedAt', 'priority'])) {
            $sortDirection = $sortDirection === 'desc' ? 'desc' : 'asc';
            $subtasksQuery->orderBy($sortBy, $sortDirection);
        }

        $subtasks = $subtasksQuery->get();

        return response()->json(['subtasks' => $subtasks], 200);
    }

    public function createSubtask(Request $request, Task $mainTaskId)
    {
        $user = $request->user();

        if ($mainTaskId->user_id !== $user->id) {
            return response()->json(['message' => 'Task not found or does not belong to the user'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status'      => 'required|in:todo',
            'priority'    => 'required|integer|between:1,5',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $subtaskData = $request->only('status', 'priority', 'title', 'description');
        $subtaskData['parent_task_id'] = $mainTaskId->id;

        $subtask = SubTask::create($subtaskData);

        return response()->json(['message' => 'Subtask created successfully', 'subtask' => $subtask], 201);
    }

    public function updateSubtask(Request $request, Task $mainTaskId, SubTask $subtaskId)
    {
        $user = $request->user();

        if ($mainTaskId->user_id !== $user->id || $subtaskId->parent_task_id !== $mainTaskId->id) {
            return response()->json(['message' => 'Task or subtask not found or does not belong to the user'], 404);
        }

        if ($subtaskId->status === 'done') {
            return response()->json(['message' => 'Completed subtask cannot be updated'], 403);
        }

        $validator = Validator::make($request->all(), [
            'priority'    => 'integer|between:1,5',
            'title'       => 'string|max:255',
            'description' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $subtaskData = $request->only('priority', 'title', 'description');
        $subtaskId->update($subtaskData);

        return response()->json(['message' => 'Subtask updated successfully', 'subtask' => $subtaskId], 200);
    }

    public function deleteSubtask(Request $request, Task $mainTaskId, SubTask $subtaskId)
    {
        $user = $request->user();

        if ($mainTaskId->user_id !== $user->id || $subtaskId->parent_task_id !== $mainTaskId->id) {
            return response()->json(['message' => 'Task or subtask not found or does not belong to the user'], 404);
        }

        if ($subtaskId->status === 'done') {
            return response()->json(['message' => 'Completed subtask cannot be deleted'], 403);
        }

        $subtaskId->delete();

        return response()->json(['message' => 'Subtask deleted successfully'], 200);
    }

    public function markSubtaskAsComplete(Request $request, Task $mainTaskId, SubTask $subtaskId)
    {
        $user = $request->user();

        if ($mainTaskId->user_id !== $user->id || $subtaskId->parent_task_id !== $mainTaskId->id) {
            return response()->json(['message' => 'Task or subtask not found or does not belong to the user'], 404);
        }

        if ($subtaskId->status === 'done') {
            return response()->json(['message' => 'Subtask is already completed'], 200);
        }

        if ($subtaskId->subtasks()->where('status', '!=', 'done')->exists()) {
            return response()->json(['message' => 'Cannot mark subtask as completed. It has outstanding subtasks'], 403);
        }

        $subtaskId->status = 'done';
        $subtaskId->completedAt = now();
        $subtaskId->save();

        return response()->json(['message' => 'Subtask marked as completed'], 200);
    }
}
