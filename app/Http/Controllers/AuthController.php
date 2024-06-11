<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Faz login do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validação dos dados da requisição
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'cpf_cnpj' => 'required', 
        ]);

        // Busca o usuário pelo e-mail fornecido
        $user = User::where('email', $request->email)->first();

        // Verifica se o usuário existe e se as credenciais estão corretas
        if (!$user || !Hash::check($request->password, $user->password) || $user->cpf_cnpj !== $request->cpf_cnpj) {
            // Lança uma exceção de validação em caso de credenciais inválidas
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Cria um token para o usuário
        $token = $user->createToken('auth-token')->plainTextToken;

        // Retorna a resposta com o token
        return response()->json(['token' => $token], 200);
    }

    /**
     * Faz logout do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Revoga o token de acesso atual do usuário
        $request->user()->currentAccessToken()->delete();

        // Retorna a resposta de logout
        return response()->json(['message' => 'Sessão encerrada'], 200);
    }
}
