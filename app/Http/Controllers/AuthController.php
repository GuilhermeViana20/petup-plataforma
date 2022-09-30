<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    * Cadastro do usuário
    */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('primeiroToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 200);
    }

    /*
    * Login do usuário
    */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Checar e-mail do usuário
        $user = User::where('email', $request->email)->first();

        // Valida usuário e checa senha
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $token = $user->createToken('primeiroToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    /*
    * Logout do usuário
    */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logout efetuado com sucesso!'
        ];
    }
}
