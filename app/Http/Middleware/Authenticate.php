<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Redireciona o usuário não autenticado para a página de login.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Redireciona para a rota de login se não for uma requisição JSON
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
