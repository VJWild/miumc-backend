<?php

namespace App\Http\Controllers\AulaVirtual;

use App\Constants\Roles;
use App\Http\Controllers\Controller;
use App\Http\Resources\AulaVirtual\SubmissionResource;
use App\Models\AulaVirtual\Assignment;
use Illuminate\Http\Request;
use App\Models\AulaVirtual\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Assignment $assignment)
    {
        $assignment->load('classroom');

        Gate::authorize('viewAny',[Submission::class,$assignment]);

        return SubmissionResource::collection($assignment->submissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Assignment $assignment)
    {
        $assignment->load('classroom.members');

        Gate::authorize('create',[Submission::class,$assignment]);

        $validated = $request->validate([
            'content' => 'string|max:255',
            'file' => 'file|max:5048',
        ]);

        $request->hasFile('file') 
            ? $filePath = $request->file('file')->store('submissions_files') 
            : $filePath = null;
        
        $validated['file_path'] = $filePath;
        $validated['user_id'] = Auth::id();

        $submission = $assignment->submissions()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Entrega realizada correctamente.',
            'submission' => new SubmissionResource($submission)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Submission $submission)
    {
        $submission->load('assignment.classroom');

        Gate::authorize('view',$submission);

        return new SubmissionResource($submission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Submission $submission)
    {
        $submission->load('assignment.classroom');

        Gate::authorize('update',$submission);

        if($request->user()->role === Roles::PROFESSOR && is_null($submission->is_graded)){
            return $this->gradeSubmission($request,$submission);
        }

        $validated = $request->validate([
            'content' => 'string|max:255',
            'file' => 'sometimes|required|file|max:5048',
        ]);

        if($request->hasFile('file')){
            if($submission->file_path){
                Storage::delete($submission->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('submissions_files');
        }

        $submission->update($validated);
        $submission->save();

        return response()->json([
            'success' => true,
            'message' => 'La entrega se ha actualizado correctamente.',
            'submission' => new SubmissionResource($submission)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Submission $submission)
    {
        $submission->load('student');
        
        Gate::authorize('delete',$submission);

        if($submission->file_path){
            Storage::delete($submission->file_path);
        }

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'La entrega se ha eliminado correctamente.'
        ]);
    }

    /**
     * Función para calificar entregas (Profesores)
     */    
    private function gradeSubmission(Request $request, Submission $submission){
        $validated = $request->validate([
            'grade' => 'required|decimal:0,20',
            'teacher_feedback' => 'string|max:255'
        ]);

        $validated['is_graded'] = true;

        $submission->update($validated);
        $submission->save();

        return response()->json([
            'success' => true,
            'message' => 'Entrega calificada correctamente.',
            'submission' => new SubmissionResource($submission) 
        ]);
    }
}
