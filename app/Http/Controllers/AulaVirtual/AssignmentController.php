<?php

namespace App\Http\Controllers\AulaVirtual;

use App\Http\Controllers\Controller;
use App\Http\Resources\AulaVirtual\AssignmentResource;
use App\Models\AulaVirtual\Assignment;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Classroom $classroom)
    {
        Gate::authorize('view',$classroom);

        return AssignmentResource::collection($classroom->assignments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Classroom $classroom,Request $request)
    {
        Gate::authorize('create',[Assignment::class,$classroom]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|max:255',
            'file' => 'file|mimes:pdf,xlsx,pptx|max:5120',
            'due_time' => ['required','date',Rule::date()->afterToday()],
            'points' => 'required|integer|between:0,20'
        ]);

        $request->hasFile('file') 
            ? $filePath = $request->file('file')->store('assignments_files') 
            : $filePath = null;

        $validated['file_path'] = $filePath;

        $assignment = $classroom->assignments()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Asignación creada correctamente.',
            'assignment' => new AssignmentResource($assignment)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        Gate::authorize('view',$assignment);

        $assignment->load(['submissions']);
        
        return new AssignmentResource($assignment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        Gate::authorize('update',$assignment);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,xlsx,pptx|max:5120',
            'due_time' => ['required','date',Rule::date()->afterToday()],
            'points' => 'required|integer|between:0,20'
        ]);

        if($request->hasFile('file')){
            if($assignment->file_path){
                Storage::delete($assignment->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('assigments_files');
        }

        $assignment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Asignación actualizada correctamente.',
            'assignment' => new AssignmentResource($assignment)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        Gate::authorize('delete',$assignment);

        if($assignment->file_path){
            Storage::delete($assignment->file_path);
        }

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asignación eliminada correctamente.'
        ]);
    }
}
