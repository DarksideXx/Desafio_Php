<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Cadastra um novo usuário.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function cadastrarUsuario(Request $request)
    {
        try {
            // Validação dos dados recebidos para o usuário
            $request->validate([
                'name' => 'required|string',
                'cpf_cnpj' => 'required|string|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'type' => 'required|in:1,2', // 1: usuário comum, 2: lojista
            ], [
                'cpf_cnpj.unique' => 'Este CPF/CNPJ já está em uso.',
                'email.unique' => 'Este e-mail já está em uso.'
            ]);

            // Criação do usuário com senha criptografada
            $user = User::create([
                'name' => $request->input('name'),
                'cpf_cnpj' => $request->input('cpf_cnpj'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'type' => $request->input('type'),
            ]);

            // Cria uma carteira para o usuário com saldo inicial zero
            $user->wallet()->create(['balance' => 0]);

            // Retornar uma resposta de sucesso
            return response()->json(['message' => 'Usuário cadastrado com sucesso.', 'user' => $user], 201);
        } catch (\Exception $e) {
            // Log do erro
            Log::error('Erro ao cadastrar usuário: ' . $e->getMessage());
            // Retornar uma resposta de erro
            return response()->json(['message' => 'Erro ao cadastrar usuário. Por favor, verifique os dados e tente novamente.'], 500);
        }
    }

    /**
     * Realiza uma transferência entre usuários.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function transferir(Request $request)
    {
        // Implementação existente para transferência...
    }
}
