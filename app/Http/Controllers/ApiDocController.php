<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Parsedown; // Importe a classe Parsedown

class ApiDocController extends Controller
{
    /**
     * Retorna a documentação da API.
     *
     * @return \Illuminate\Http\Response
     */
    public function docs()
    {
        // Ler o conteúdo do arquivo README.md
        $readmePath = base_path('README.md');
        $readmeContent = file_exists($readmePath) ? file_get_contents($readmePath) : '';

        // Converter Markdown para HTML usando Parsedown
        $parsedown = new Parsedown();
        $htmlContent = $parsedown->text($readmeContent);

        // Adicionar estilos CSS ao HTML
        $styledHtmlContent = '<html><head><link rel="stylesheet" href="/css/styles.css"></head><body>' . $htmlContent . '</body></html>';

        return Response::make($styledHtmlContent);
    }
}
