<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    /**
     * Cria uma nova instância do controlador.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware para autenticação requerida em todas as rotas deste controlador
        $this->middleware('auth');
    }

    /**
     * Mostra o painel da aplicação.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Retorna a view "home"
        return view('home');
    }
}
