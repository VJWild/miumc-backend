<?php

namespace App\Http\Controllers\AulaVirtual;

use App\Http\Controllers\Controller;
use App\Http\Resources\AulaVirtual\SubmissionResource;
use App\Models\AulaVirtual\Assignment;
use Illuminate\Http\Request;
use App\Models\AulaVirtual\Submission;
use Illuminate\Support\Facades\Gate;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Assignment $assignment)
    {
        Gate::authorize('viewAny',[Submission::class,$assignment]);

        return SubmissionResource::collection($assignment->submissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'classroom_user_id' => 'required|exists:classroom_user,user_id',
            'content' => 'string|max:255',
            'file' => 'file|max:5048',
        ]);

        $request->hasFile('file') ? $filePath = $request->file('file')->store('submissions_files') : $filePath = null;
        
        $validated['file_path'] = $filePath;

        $submission = Submission::create($validated);

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
        return new SubmissionResource($submission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Submission $submission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Submission $submission)
    {
        //
    }
}
