<?php

namespace App\Rest\Controllers;

use App\Models\Task;
use App\Rest\Controllers\Controller;
use App\Rest\Resources\TaskResource;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = TaskResource::class;

    public function uploadAttachment(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'attachment' => 'required|file|max:20480',
        ]);

        $path = $request->file('attachment')->store('attachments');

        $task = Task::find($request->task_id);
        $task->attachment_path = $path;
        $task->save();

        return response()->json([
            'message' => 'fichier uploader', 'url' => asset('storage/' . $path)
        ]);
    }

}
