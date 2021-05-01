<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function create_token(Request $request): JsonResponse
    {
        $validData = $request->validate([
            'username' => ['required', 'string', 'exists:admins'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('username', $validData['username'])->first();

        abort_if(
            !$admin || !Hash::check($validData['password'], $admin->password),
            401,
            'The provided credentials are incorrect.'
        );

        activity("Admin")
            ->withProperties([
                ['IP' => $request->ip()],
                ['username' => $request->input('username')],
            ])
            ->log('Successful Login');

        return response()->json([
            "token" => $admin->createToken($admin->full_name)->plainTextToken
        ]);
    }
}
