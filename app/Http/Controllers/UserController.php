<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    function teste() {
        return response()->json(['ok'=>true]);
    }

    function envio(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        return response()->json(['ok'=>true, 'email' => $validated['email']]);
    }

    function getUser(): JsonResponse
    {
        // $users = User::select('id', 'name', 'email')->orderBy('id', 'DESC')->get();
        $users = User::orderBy('id', 'ASC')->paginate(5);
        // $users = User::all();

        return response()->json([
            'status' => true,
            'users' => $users
        ], 200);

        // return $users;
    }

    function getUserById($id): User
    {
       $user = User::find($id);

    //    return response()->json([
    //     'status' => true,
    //     'user' => $user
    //    ], Response::HTTP_OK);

        return $user;
    }
}
