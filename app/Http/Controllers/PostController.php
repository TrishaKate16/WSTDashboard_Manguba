<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return response()->json(Post::all());
    }

    public function create()
    {

    }
     
    public function store(Request $request)
    {
        $post = Post::create($request->all());
        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }
}
