<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request) {
        return $request->user()->projects()->get();
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $project = $request->user()->projects()->create($data);
        return response()->json($project, 201);
    }

    public function show(Request $request, Project $project) {
        $this->authorize('view', $project);
        return $project;
    }

    public function update(Request $request, Project $project) {
        $this->authorize('update', $project);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $project->update($data);

        return response()->json($project, 200);
    }

    public function destroy(Request $request, Project $project) {

        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(null, 204);
    }

    public function restore(Request $request, $id) {
        $project = Project::withTrashed()->findOrFail($id);

        $this->authorize('restore', $project);

        $project->restore();

        return response()->json($project, 200);
    }
}
