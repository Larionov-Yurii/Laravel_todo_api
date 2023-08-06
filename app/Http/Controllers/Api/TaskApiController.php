<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskApiController extends Controller
{
    public function getMainTasks(Request $request)
    {
        $tasksQuery = Task::query();

        if ($request->has('status')) {
            $status = $request->input('status');
            $tasksQuery->where('status', $status);
        }

        if ($request->has('priority_from') && $request->has('priority_to')) {
            $priorityFrom = $request->input('priority_from');
            $priorityTo = $request->input('priority_to');
            $tasksQuery->whereBetween('priority', [$priorityFrom, $priorityTo]);
        }

        if ($request->has('title')) {
            $title = $request->input('title');
            $tasksQuery->where('title', 'like', '%' . $title . '%');
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction', 'asc');

            if ($sortBy === 'priority') {
                $tasksQuery->orderBy('priority', $sortDirection);
            } elseif (in_array($sortBy, ['createdAt', 'completedAt'])) {
                $tasksQuery->orderBy($sortBy, $sortDirection);
            }
        }

        $tasks = $tasksQuery->get();

        return response()->json(['tasks' => $tasks], 200);
    }

    public function createMainTask(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'status'      => 'required|in:todo',
            'priority'    => 'required|integer|between:1,5',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'parent_id'   => 'nullable|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $taskData = $request->only('status', 'priority', 'title', 'description');
        $taskData['user_id'] = $user->id;

        if ($request->has('parent_id')) {
            $parentTask = Task::where('user_id', $user->id)->find($request->input('parent_id'));
            if (!$parentTask) {
                return response()->json(['message' => 'Parent task not found or does not belong to the user'], 404);
            }

            $task = $parentTask->subtasks()->create($taskData);
            return response()->json(['message' => 'Subtask created successfully', 'task' => $task], 201);
        }

        $task = Task::create($taskData);
        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function updateMainTask(Request $request, $mainTaskId)
    {
        $user = $request->user();

        $task = Task::where('user_id', $user->id)->find($mainTaskId);

        if (!$task) {
            return response()->json(['message' => 'Task not found or does not belong to the user'], 404);
        }

        if ($task->status === 'done') {
            return response()->json(['message' => 'Completed task cannot be updated'], 403);
        }

        $validator = Validator::make($request->all(), [
            'priority'    => 'integer|between:1,5',
            'title'       => 'string|max:255',
            'description' => 'string',
            'parent_id'   => 'nullable|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $taskData = $request->only('priority', 'title', 'description');

        if ($request->has('parent_id')) {
            $parentTask = Task::where('user_id', $user->id)->find($request->input('parent_id'));
            if (!$parentTask) {
                return response()->json(['message' => 'Parent task not found or does not belong to the user'], 404);
            }
            $taskData['parent_id'] = $request->input('parent_id');
        }

        $task->update($taskData);

        return response()->json(['message' => 'Task updated successfully', 'task' => $task], 200);
    }

    public function deleteMainTask(Request $request, $mainTaskId)
    {
        $user = $request->user();

        $task = Task::where('user_id', $user->id)->with('subtasks')->find($mainTaskId);

        if (!$task) {
            return response()->json(['message' => 'Task not found or does not belong to the user'], 404);
        }

        if ($task->status === 'done') {
            return response()->json(['message' => 'Cannot delete a completed task'], 403);
        }

        if ($task->subtasks()->exists()) {
            return response()->json(['message' => 'Cannot delete a task with subtasks'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }

    public function markMainTaskAsComplete(Request $request, $mainTaskId)
    {
        $user = $request->user();

        $task = Task::where('user_id', $user->id)->find($mainTaskId);

        if (!$task) {
            return response()->json(['message' => 'Task not found or does not belong to the user'], 404);
        }

        if ($task->subtasks()->where('status', '!=', 'done')->exists()) {
            return response()->json(['message' => 'Task has outstanding subtasks'], 403);
        }

        if ($task->status === 'done') {
            return response()->json(['message' => 'Task is already completed'], 200);
        }

        $task->status = 'done';
        $task->completedAt = now();
        $task->save();

        return response()->json(['message' => 'Task marked as completed'], 200);
    }
}
