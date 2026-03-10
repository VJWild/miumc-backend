<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicRecordController extends Controller
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function bulkUpdate(Request $request, User $user){
        $approvedSubjectCodes = $request->input("approvedSubjectCodes");

        try {
            DB::transaction(function () use ($approvedSubjectCodes, $user) {
                $user->academic_records()->delete();

                if($approvedSubjectCodes && count($approvedSubjectCodes) > 0){
                    $subjects = Subject::getManyByCodesCollection($approvedSubjectCodes);

                    if(count($subjects) > 0){
                        $createValues = $subjects->map(function ($s){
                            return [
                                "subject_id" => $s->id,
                                "status" => "aprobada"
                            ];
                        });
                        $user->academic_records()->createMany($createValues);
                    }
                }
            });

            return response()->json([
                "success" => true,
                "message" => "Record actualizado con éxito"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error en bulk update: " . $e->getMessage() 
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
