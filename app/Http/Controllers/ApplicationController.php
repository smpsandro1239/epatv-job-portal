<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:jobs_employment,id',
            'cover_letter' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $application = Application::create([
            'user_id' => auth('api')->id(),
            'job_id' => $request->job_id,
            'status' => 'pending',
            'cover_letter' => $request->cover_letter,
        ]);
        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application
        ], 201);
    }
}
