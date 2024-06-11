<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TransferService;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransferServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a transferência de dinheiro com sucesso.
     *
     * @return void
     */
    public function testTransferSuccess()
    {
        Log::info('Starting testTransferSuccess');

        // Cria usuários pagador e recebedor
        $payer = User::factory()->create(['type' => 1]); // usuário comum
        $payee = User::factory()->create(['type' => 2]); // lojista

        // Configura as carteiras dos usuários
        $payer->wallet()->create(['balance' => 500]);
        $payee->wallet()->create(['balance' => 0]);

        // Simula respostas de serviços externos
        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response(['message' => 'Autorizado'], 200),
            'https://util.devi.tools/api/v1/notify' => Http::response(['message' => 'Notificação enviada'], 200),
        ]);

        // Cria instância do serviço de transferência com o HttpClient injetado
        $transferService = new TransferService(new HttpClient());
        $transferService->transfer($payer, $payee, 100);

        // Verifica os saldos das carteiras após a transferência
        $this->assertEquals(400, $payer->wallet->fresh()->balance);
        $this->assertEquals(100, $payee->wallet->fresh()->balance);

        Log::info('testTransferSuccess successful');
    }

    /**
     * Testa a falha da transferência devido a fundos insuficientes.
     *
     * @return void
     */
    public function testTransferFailsDueToInsufficientFunds()
    {
        Log::info('Starting testTransferFailsDueToInsufficientFunds');

        // Cria usuários pagador e recebedor
        $payer = User::factory()->create(['type' => 1]); // usuário comum
        $payee = User::factory()->create(['type' => 2]); // lojista

        // Configura as carteiras dos usuários
        $payer->wallet()->create(['balance' => 50]);
        $payee->wallet()->create(['balance' => 0]);

        // Cria instância do serviço de transferência com o HttpClient injetado
        $transferService = new TransferService(new HttpClient());

        // Define as expectativas de exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Saldo insuficiente.');

        // Tenta realizar a transferência
        $transferService->transfer($payer, $payee, 100);

        Log::info('testTransferFailsDueToInsufficientFunds successful');
    }

    /**
     * Testa a falha da transferência porque o pagador é um lojista.
     *
     * @return void
     */
    public function testTransferFailsBecausePayerIsMerchant()
    {
        Log::info('Starting testTransferFailsBecausePayerIsMerchant');

        // Cria usuários pagador e recebedor
        $payer = User::factory()->create(['type' => 2]); // lojista
        $payee = User::factory()->create(['type' => 1]); // usuário comum

        // Configura as carteiras dos usuários
        $payer->wallet()->create(['balance' => 500]);
        $payee->wallet()->create(['balance' => 0]);

        // Cria instância do serviço de transferência com o HttpClient injetado
        $transferService = new TransferService(new HttpClient());

        // Define as expectativas de exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Lojistas não podem enviar transferências.');

        // Tenta realizar a transferência
        $transferService->transfer($payer, $payee, 100);

        Log::info('testTransferFailsBecausePayerIsMerchant successful');
    }
}
