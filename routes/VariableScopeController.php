<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VariableScopeController extends Controller
{
    public function index()
    {
        $student = "Trisha";
        $message = "Hello, World, $student";

        return response()->json([
            'student' => $student,
            'message' => $message
        ]);
    }
}