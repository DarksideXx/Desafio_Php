<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Este namespace é aplicado às rotas dos seus controllers.
     *
     * Além disso, é definido como o namespace raiz do gerador de URL.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define seus "route model bindings", filtros de padrão, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define as rotas para a aplicação.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define as rotas "web" para a aplicação.
     *
     * Essas rotas recebem todos os estados de sessão, proteção CSRF, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define as rotas "api" para a aplicação.
     *
     * Essas rotas geralmente são stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
