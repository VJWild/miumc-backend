<?php

namespace App\Http\Controllers\AulaVirtual;

use App\Constants\Roles;
use App\Http\Controllers\Controller;
use App\Http\Resources\AulaVirtual\ClassroomResource;
use App\Models\AulaVirtual\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        switch ($user->role) {
            case Roles::PROFESSOR:
                $classrooms = $user->managedClassrooms()->withCount('members')->get();
                break;
            
            default:
                $classrooms = $user->enrolledClassrooms()->with('professor')->get();
                break;
        }

        return ClassroomResource::collection($classrooms);
    }

    /**
     * Funcion para "unirse" a una classroom.
     */
    public function join(Request $request){
        $request->validate([
            'access_code' => 'required|string|max:6'
        ]);

        $classroom = Classroom::query()->where('access_code',$request->access_code)->firstOrFail();

        if($request->user()->enrolledClassrooms()->where('classroom_id',$classroom->id)->exists()){
            return response()->json([
                'success' => false,
                'message' => "Ya eres miembro de esta Aula Virtual."
            ]);
        }

        $request->user()->enrolledClassrooms()->syncWithoutDetaching([$classroom->id]);

        return response()->json([
            'success' => true,
            'message' => 'Te has unido a el aula  "'. $classroom->title . '" exitosamente.',
            'classroom' => new ClassroomResource($classroom)
        ]);
    }
    
    /**
     * Función para "abandonar" una classroom.
     */
    public function leave(Request $request, Classroom $classroom){
        Gate::authorize('leave',$classroom);

        $request->user()->enrolledClassrooms()->detach($classroom->id);

        return response()->json([
            'success' => true,
            'message' => 'Ha abandonado el aula virtual exitosamente.'
        ]);
    }

    public function kick(Classroom $classroom, User $user){
        Gate::authorize('kickUser',$classroom);

        $classroom->members()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Usuario expulsado del aula virtual exitosamente.'
        ]); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create',Classroom::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'sometimes|string|max:255',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = $request->user();

        $request->hasFile('cover') 
            ? $coverPath = $request->file('cover')->store('covers','public') 
            : $coverPath = null;

        $accessCode = Classroom::generateUniqueCode();

        $validated['cover_path'] = $coverPath;
        $validated['access_code'] = $accessCode;

        $classroom = $user->managedClassrooms()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Aula virtual creada correctamente',
            'classroom' => new ClassroomResource($classroom)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom, Request $request)
    {
        Gate::authorize('view',$classroom);

        $classroom->load(['professor','members','assignments','posts'])
                  ->loadCount(['members','assignments','posts']);

        return new ClassroomResource($classroom);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        Gate::authorize('update',$classroom);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'sometimes|string|max:255',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if($request->hasFile('cover')){
            if($classroom->cover_path !== 'covers/cover.jpg'){
                Storage::delete($classroom->cover_path);
            }
            $validated['cover_path'] = $request->file('cover')->store('covers','public');
        }

        $classroom->update($validated);

        return response()->json([
            'success'=>true,
            'message'=> 'Aula virtual actualizada correctamente',
            'classroom' => new ClassroomResource($classroom)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Classroom $classroom)
    {
        Gate::authorize('delete',$classroom);

        if($classroom->cover_path !== 'covers/cover.jpg'){
            Storage::delete($classroom->cover_path);
        }

        $classroom->delete();

        return response()->json([
            'success' => true,
            'message' => "Aula virtual elimida correctamente"
        ]);
    }
}
