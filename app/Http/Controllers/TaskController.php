<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index($projectId)
    {
        $project = auth()->user()->projects()->findOrFail($projectId);

        return $project->tasks()->get();
    }

    public function store(Request $request, $projectId)
    {
        $project = auth()->user()->projects()->findOrFail($projectId);

        $validated = $request->validate([
            'title'=> 'required|string|max:255',
            'content'=> 'required|string',
            'is_completed'=> 'required|boolean',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('tasks_public', 'public');
        }

        $task = $project->tasks()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_completed' => $validated['is_completed'] ?? false,
            'attachment_path' => $path,
        ]);

        event(new \App\Events\TaskCreated($task));

        return response()->json(['message' => 'task created', 'task' => $task], 200);
    }

    public function show($projectId, $taskId) {
        $project = auth()->user()->projects()->findOrFail($projectId);

        $task = $project->tasks()->findOrFail($taskId);

        return response()->json($task, 200);
    }

    public function update(Request $request, $projectId, $taskId)
    {
        $project = auth()->user()->projects()->findOrFail($projectId);
        $task = $project->tasks()->findOrFail($taskId);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'is_completed' => 'sometimes|boolean',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        if ($request->hasFile('file')) {
            if ($task->attachment_path) {
                \Storage::disk('public')->delete($task->attachment_path);
            }

            $validated['attachment_path'] = $request->file('file')->store('tasks_public', 'public');
        }
            $task->update($validated);

            return response()->json(['message' => 'task updated', 'task' => $task->fresh()], 200);
    }

    public function destroy($projectId, $taskId) {

        $project = auth()->user()->projects()->findOrFail($projectId);
        $task = $project->tasks()->findOrFail($taskId);

        if($task->attachment_path) {
                \Storage::disk('public')->delete($task->attachment_path);
            }

        $task->delete();

        return response()->json(['message' => 'task deleted'], 200);
    }
}
