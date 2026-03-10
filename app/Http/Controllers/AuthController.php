<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Constants\Roles;
use App\Models\AcademicRecord;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request){
        //Se validan los datos del Request
        $validated = $request->validate([
            "studentCode" => "required|string|max:255",
            "password" => "required|string|min:8"
        ]);

        //Se obtienen los datos del usuario de la bdd
        $user = User::findByCodeWithRelationships($validated['studentCode']);

        //Validación de los datos de logeo
        if(!$user || !Hash::check($validated['password'],$user->password_hash)){
            return response()->json([
                "success" => false,
                "message" => "Las credenciales proporcionadas son incorrectas",
                "error" => 'auth_failed'
            ],401);
        }

        //Creación del token Sanctum
        $nameArr = explode(" ",$user->full_name);
        $deviceName = $request->header('User-Agent') ?: 'Unknown';
        $tokenName = join("",$nameArr) . "_" . $deviceName;

        //Asginación de permisos según el rol
        $permissions = match($user->role) {
            'admin' => [Roles::ADMIN],
            'cadete' => [Roles::CADET],
            default => [],
        };

        $tokenStr = $user->createToken($tokenName,$permissions)->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'access_token' => $tokenStr,
            'token_type' => 'Bearer'
        ]);
    }

    public function register(Request $request){
        $validated = $request->validate([
            "studentCode" => "required|string|max:255",
            "email" => "required|string|email|unique:users",
            "password" => "required|string|min:8|confirmed"
        ]);

        User::create([
            "student_code" => $validated['studentCode'],
            "email" => $validated['email'],
            "password_hash" => Hash::make($validated['password'])
        ]);

        return response()->json([
            "success" => true,
            "message" => "Usuario creado correctamente"
        ], 200);
    }

    public function completeOnboarding(Request $request){
        $validated = $request->validate([
            "studentCode" => "required|string|max:255|exists:users,student_code",
            "fullName" => "required|string|max:255",
            "age" => "required|integer|max:120",
            "birthDate" => [
                "required",
                "date",
                Rule::date()->format("Y-m-d"),
                Rule::date()->beforeOrEqual(today()->subYears($request->input("age")))
            ],
            "phone" => "required|string|regex:/^0\d{3}-\d{7}$/",
            "mencionId" => "required|integer",
            "approvedSubjects" => "required"
        ]);

        try {
            DB::transaction(function () use ($validated){
                $user = User::getByCode($validated['studentCode']);

                $user->update([
                    "full_name" => $validated['fullName'],
                    "age" => $validated['age'],
                    "birth_date" => $validated['birthDate'],
                    "phone" => $validated['phone'],
                    "specialization_id" => $validated['mencionId'],
                ]);

                if($validated['approvedSubjects'] && count($validated['approvedSubjects']) > 0){
                    $subjects = Subject::getManyByCodesCollection(collect($validated['approvedSubjects']));
                    if(count($subjects) > 0){
                        $createValues = $subjects->map(function ($s){
                            return [
                                "subject_id" => $s->id,
                                "status" => 'aprobada'
                            ];
                        });

                        $user->academic_records()->createMany($createValues);
                    }
                }

                return response()->json([
                    "success" => true,
                    "message" => "Onboarding realizado exitosamente. ¡Bienvenido a bordo! ⚓🚢"
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error en Onboarding: " . $e->getMessage() 
            ],500);
        }
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json(["message" => 'Sesión cerrada']);
    }
}
