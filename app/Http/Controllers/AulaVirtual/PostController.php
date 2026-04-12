<?php

namespace App\Http\Controllers\AulaVirtual;

use App\Http\Controllers\Controller;
use App\Http\Resources\AulaVirtual\PostResource;
use App\Models\AulaVirtual\Post;
use App\Models\AulaVirtual\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Classroom $classroom)
    {
        Gate::authorize('viewAny',[Post::class,$classroom]);

        return PostResource::collection($classroom->posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Classroom $classroom)
    {
        Gate::authorize('create',[Post::class,$classroom]);

        $validated = $request->validate([
            'type' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
            'file' => 'sometimes|required|file|max:1028'
        ]);

        $request->hasFile('file') 
            ? $filePath = $request->file('file')->store('assignments_files') 
            : $filePath = null;
        
        $validated['user_id'] = Auth::id();
        $validated['file_path'] = $filePath;

        $post = $classroom->posts()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'El post se ha creado correctamente.',
            'post' => new PostResource($post)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load('classroom.members');

        Gate::authorize('view',$post);

        $post->classroom->unsetRelation('members');

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->load('classroom');
        Gate::authorize('delete',$post);

        if($post->file_path){
            Storage::delete($post->file_path);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post eliminado exitosamente.'
        ]);
    }
}
