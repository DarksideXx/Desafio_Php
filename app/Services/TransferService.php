<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TransferService
{
    protected HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Realiza uma transferência entre usuários.
     *
     * @param \App\Models\User $payer
     * @param \App\Models\User $payee
     * @param float $amount
     * @return void
     * @throws \Exception
     */
    public function transfer(User $payer, User $payee, float $amount): void
    {
        try {
            // Verificar se o pagador é um lojista
            if ($payer->type == 2) {
                throw new \Exception('Usuários do tipo 2 (lojistas) não podem realizar transferências.');
            }

            // Validar se o usuário pagador tem saldo suficiente
            if ($payer->wallet->balance < $amount) {
                throw new \Exception('Saldo insuficiente para realizar a transferência.');
            }

            // Transferência de dinheiro como uma transação
            DB::transaction(function () use ($payer, $payee, $amount) {
                // Deduz o valor da carteira do pagador
                $payer->wallet->decrement('balance', $amount);

                // Adiciona o valor à carteira do beneficiário
                $payee->wallet->increment('balance', $amount);
            });

            // Registra métricas
            $this->recordMetrics($amount);
        } catch (\Exception $e) {
            Log::error('Transferência falhou: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Registra métricas da transferência.
     *
     * @param float $amount
     * @return void
     */
    private function recordMetrics(float $amount): void
    {
        Log::info("Transferência concluída: Valor transferido: $amount");
    }
}
