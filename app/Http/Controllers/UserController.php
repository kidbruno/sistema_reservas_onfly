<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    function InsertUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome'   => 'required|string|max:60',
            'idade'  => 'nullable|integer|min:0|max:99',
            'email'  => 'required|email|max:100|unique:usuarios,email',
            'senha'  => 'required|string|min:0|max:6',
            'status' => 'in:ativo,cancelado,suspenso',
        ]);

        $validated['senha'] = md5($validated['senha']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'Usuário inserido com sucesso. Id: ' . $user->Id,
        ], 
        Response::HTTP_OK);
    }

    function getUser(): JsonResponse
    {
        $users = User::orderBy('Id', 'ASC')->paginate(5);

        $formattedUsers = $users->map(function($user) {
            return [
                'nome' => $user->nome,
                'idade' => $user->idade,
                'email' => $user->email,
                'status' => $user->status,
            ];
        });

        return response()->json([
            'usuarios' => $formattedUsers
        ], Response::HTTP_OK);
    }

    function getUserById($id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'Usuário não encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'nome' => $user->nome,
            'idade' => $user->idade,
            'email' => $user->email,
            'status' => $user->status,
        ], Response::HTTP_OK);
    }

    function deleteUser($id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'Usuário não encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user->status === 'cancelado') {
            return response()->json([
                'message' => 'Usuário já está inativado.',
            ], Response::HTTP_OK);
        }

        $user->status = 'cancelado';
        $user->save();

        return response()->json([
            'message' => 'Usuário inativado com sucesso.',
        ], Response::HTTP_OK);
    }
}
