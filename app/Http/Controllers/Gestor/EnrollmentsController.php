<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class EnrollmentsController extends Controller
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
        try {
            DB::transaction(function () use ($request){
                $studentCode = $request->input("studentCode");
                $period = $request->input("period","2026-I");
                $enrolledSubjects = collect($request->input("enrolledSubjects"));

                $user = User::getByCode($studentCode);

                if(!$user){
                    return response()->json([
                        "success" => false,
                        "message" => "Usuario no encontrado."
                    ],404);
                }

                if(!$enrolledSubjects || $enrolledSubjects < 0){
                    return response()->json([
                        "success" => false,
                        "message" => "No se han encontrado materias para inscribir"
                    ],400);
                }
                
                //Limpiamos la inscripción anterior de ese periodo
                $user->enrollments()
                    ->where('period',$period)
                    ->delete();

                //Insertamos las nuevas materias
                $codes = $enrolledSubjects->pluck('code');
                $subjects = Subject::getManyByCodesCollection($codes);

                $createValues = $enrolledSubjects->map(function ($inscrita) use ($period, $subjects) {
                    $subject = $subjects->firstWhere('code',$inscrita->codigo);
                    $scheduleData = json_encode([
                        "day" => $inscrita->day,
                        "dayIdx" => $inscrita->dayIdx,
                        "startTime" => $inscrita->startTime,
                        "endTime" => $inscrita->endTime,
                        "room" => $inscrita->room,
                        "color" => $inscrita->color,
                        "duration" => $inscrita->duration,
                        "professor" => $inscrita->professor
                    ]);

                    return [
                        "subject_id" => $subject->id,
                        "period" => $period,
                        "schedule_data" => $scheduleData,
                        "created_at" => now()
                    ];
                });

                $user->enrollments()->createMany($createValues);
            });

            return response()->json([
                    "success" => true,
                    "message" => "Inscripción guardada correctamente"
                ],
            200);
                
        } catch (\Exception $e) {

        }


        
        
        

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
