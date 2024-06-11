<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TransferService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentNotification;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    protected TransferService $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * Realiza uma transferência entre usuários.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(Request $request): JsonResponse
    {
        try {
            // Verificar se os campos necessários existem na requisição
            $fields = ['value', 'payer', 'payee'];
            foreach ($fields as $field) {
                if (!$request->has($field)) {
                    throw new \Exception("Campo '$field' é obrigatório.");
                }
            }

            // Validar valor
            $value = $request->input('value');
            if (!is_numeric($value) || $value <= 0) {
                throw new \Exception('O valor da transferência deve ser um número positivo.');
            }

            // Verificar se o pagador é um usuário
            $payerId = $request->input('payer');
            $payer = User::find($payerId);
            if (!$payer) {
                throw new \Exception('O pagador não foi encontrado.');
            }

            // Verificar se o beneficiário é um usuário
            $payeeId = $request->input('payee');
            $payee = User::find($payeeId);
            if (!$payee) {
                throw new \Exception('O beneficiário não foi encontrado.');
            }

            // Realizar a transferência de dinheiro entre as carteiras
            $this->transferService->transfer($payer, $payee, $value);

            // Se tudo estiver correto, retorna uma resposta JSON de sucesso
            return response()->json(['message' => 'Transferência realizada com sucesso.'], 200);
        } catch (\Exception $e) {
            // Log em caso de falha na transferência
            Log::error("Falha na transferência: {$e->getMessage()}");

            return response()->json(['message' => 'Transferência falhou: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Método para enviar notificação de recebimento de pagamento por e-mail.
     *
     * @param User $recipient
     * @return void
     */
    protected function sendNotificationByEmail(User $recipient): void
    {
        try {
            // Aqui você pode ajustar a lógica para construir o conteúdo do e-mail
            $subject = 'Você recebeu um pagamento em sua conta';
            $message = 'Olá, Você recebeu um pagamento em sua conta.';

            // Envie o e-mail usando o driver 'log'
            Mail::mailer('log')->to($recipient->email)->send(new PaymentNotification($subject, $message));

            Log::info("Notificação por e-mail enviada com sucesso para {$recipient->email}");
        } catch (\Exception $e) {
            Log::error("Falha ao enviar notificação por e-mail para {$recipient->email}: " . $e->getMessage());
        }
    }

    /**
     * Método para autorizar a transferência consultando o serviço externo.
     *
     * @param float $value
     * @param int $payerId
     * @param int $payeeId
     * @throws \Exception
     * @return array
     */
    public function authorizeTransfer(float $value, int $payerId, int $payeeId): array
    {
        $url = 'https://util.devi.tools/api/v2/authorize';

        try {
            $client = new GuzzleClient();

            // Faz a requisição GET com os parâmetros na URL
            $response = $client->get($url, [
                'query' => [
                    'value' => $value,
                    'payer' => $payerId,
                    'payee' => $payeeId,
                ],
                'verify' => false, // Desabilita a verificação SSL temporariamente
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            // Verifica se a resposta contém o status de sucesso
            if ($response->getStatusCode() === 200 && isset($responseData['status']) && $responseData['status'] === 'success') {
                return $responseData;
            } else {
                throw new \Exception('Falha na autorização da transferência.');
            }
        } catch (RequestException $e) {
            // Em caso de erro na requisição
            throw new \Exception('Erro na requisição para o serviço externo: ' . $e->getMessage());
        }
    }
}
