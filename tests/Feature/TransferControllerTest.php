<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Services\TransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransferControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a transferência de dinheiro com sucesso.
     *
     * @return void
     */
    public function testTransferEndpointSuccess()
    {
        Log::info('Starting testTransferEndpointSuccess');

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

        // Faz a solicitação de transferência
        $response = $this->actingAs($payer)->postJson('/transfer', [
            'value' => 100,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        // Verifica a resposta
        $response->assertStatus(200)
            ->assertJson(['message' => 'Transfer successful']);

        // Verifica os saldos das carteiras após a transferência
        $this->assertEquals(400, $payer->wallet->fresh()->balance);
        $this->assertEquals(100, $payee->wallet->fresh()->balance);

        Log::info('testTransferEndpointSuccess successful');
    }

    /**
     * Testa a transferência de dinheiro com saldo insuficiente.
     *
     * @return void
     */
    public function testTransferEndpointInsufficientFunds()
    {
        Log::info('Starting testTransferEndpointInsufficientFunds');

        // Cria usuários pagador e recebedor
        $payer = User::factory()->create(['type' => 1]); // usuário comum
        $payee = User::factory()->create(['type' => 2]); // lojista

        // Configura as carteiras dos usuários
        $payer->wallet()->create(['balance' => 50]);
        $payee->wallet()->create(['balance' => 0]);

        // Faz a solicitação de transferência
        $response = $this->actingAs($payer)->postJson('/transfer', [
            'value' => 100,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        // Verifica a resposta
        $response->assertStatus(400)
            ->assertJson(['message' => 'Saldo insuficiente para realizar a transferência.']);

        Log::info('testTransferEndpointInsufficientFunds successful');
    }

    /**
     * Testa a falha da transferência porque o pagador é um lojista.
     *
     * @return void
     */
    public function testTransferEndpointFailsBecausePayerIsMerchant()
    {
        Log::info('Starting testTransferEndpointFailsBecausePayerIsMerchant');

        // Cria usuários pagador e recebedor
        $payer = User::factory()->create(['type' => 2]); // lojista
        $payee = User::factory()->create(['type' => 1]); // usuário comum

        // Configura as carteiras dos usuários
        $payer->wallet()->create(['balance' => 500]);
        $payee->wallet()->create(['balance' => 0]);

        // Faz a solicitação de transferência
        $response = $this->actingAs($payer)->postJson('/transfer', [
            'value' => 100,
            'payer' => $payer->id,
            'payee' => $payee->id,
        ]);

        // Verifica a resposta
        $response->assertStatus(400)
            ->assertJson(['message' => 'Lojistas não podem enviar transferências.']);

        Log::info('testTransferEndpointFailsBecausePayerIsMerchant successful');
    }
}
