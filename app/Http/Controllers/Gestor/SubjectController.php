<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Gestor\SubjectResource;
use App\Models\Gestor\AcademicRecord;
use App\Models\Gestor\Enrollment;
use App\Models\Gestor\Specialization;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function getBySpecialization(Specialization $specialization){
        $subjects = $specialization->subjects()
                                   ->orWhereNull("specialization_id")
                                   ->orderBy('semester','asc')
                                   ->get();

        return SubjectResource::collection($subjects); 
    }

    public function getEnrolled($code = null){
        return SubjectResource::collection(Enrollment::getEnrolledSubjectsByStudentCode($code));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getStudentProgress($code = null){
        $progress = AcademicRecord::getApprovedSubjectsByStudentCode($code);
        return response()->json($progress->pluck('code'));

    }
}
