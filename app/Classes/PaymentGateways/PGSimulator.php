<?php

namespace App\Classes\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTO\PaymentRefundDTO;
use App\DTO\PaymentRequestDTO;
use App\DTO\PaymentResponseDTO;
use App\Enums\ConnectionType;
use App\Enums\PaymentGatewayRequestType;
use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Models\PaymentGatewayConnectionApiLog;
use App\Models\Transaction;
use Brick\Math\RoundingMode;
use Carbon\CarbonImmutable;
use Devhammed\LaravelBrickMoney\Money;
use Illuminate\Support\Str;

/**
 * Simulates a live payment gateway for local/dev/QA use: instead of calling
 * out to a real bank/PG, it sends the customer to a locally hosted checkout
 * page (PGSimulatorController) where the outcome (success/failed/pending)
 * is chosen manually, which then posts back through the normal
 * handlePaymentResponse callback flow like any real gateway would.
 */
class PGSimulator implements PaymentGatewayInterface
{
    protected bool $feesIncludedInAmount;

    protected float $feesRate;

    protected bool $isRefundSupported;

    protected ConnectionType $connectionType;

    /**
     * @param  array<string, mixed>  $pg_data
     */
    public function __construct(array $pg_data, ConnectionType|string $connectionType = ConnectionType::TEST)
    {
        $this->feesIncludedInAmount = (bool) ($pg_data['fees_included_in_amount'] ?? false);
        $this->feesRate = (float) ($pg_data['fees_rate'] ?? 0);
        $this->isRefundSupported = (bool) ($pg_data['supports_refunds'] ?? true);

        $this->connectionType = $connectionType instanceof ConnectionType
            ? $connectionType
            : ConnectionType::from($connectionType);
    }

    public function calculateFees(Money $amount): Money
    {
        return $amount->multipliedBy($this->feesRate / 100, RoundingMode::HALF_UP);
    }

    public function handlePaymentRequest(PaymentRequestDTO $paymentRequest, Transaction $transaction): string
    {
        return route('pgSimulatorCheckout', ['transaction' => $transaction->id]);
    }

    /**
     * @param  array<string, mixed>  $requestData
     * @param  array<string, mixed>  $responseData
     */
    protected function logSimulatedApiCall(Transaction $transaction, PaymentGatewayRequestType $requestType, array $requestData, array $responseData): void
    {
        PaymentGatewayConnectionApiLog::create([
            'client_id' => $transaction->client_id,
            'pg_connection_id' => $transaction->pg_connection_id,
            'transaction_id' => $transaction->id,
            'request_type' => $requestType,
            'request_data' => $requestData,
            'response_data' => $responseData,
            'response_status' => '200',
        ]);
    }

    /**
     * @param  array<string, mixed>  $response
     */
    public function handlePaymentResponse(array $response): PaymentResponseDTO
    {
        $transaction = Transaction::with(['client', 'pgConnection'])->find((string) ($response['transactionDbId'] ?? ''));

        if (! $transaction) {
            throw new \Exception('Transaction not found.');
        }

        $status = TransactionStatus::tryFrom((string) ($response['status'] ?? '')) ?? TransactionStatus::FAILED;
        $amount = $transaction->amount['amount'];
        $pgFees = isset($response['pgFees']) && is_numeric($response['pgFees'])
            ? Money::of($response['pgFees'], $transaction->currency)
            : $this->calculateFees($amount);
        $paymentMethod = PaymentMethod::tryFrom((string) ($response['paymentMethod'] ?? '')) ?? PaymentMethod::UNKNOWN;
        $transactionId = (string) ($response['transactionId'] ?? ('SIM'.strtoupper(Str::random(12))));

        $this->logSimulatedApiCall($transaction, PaymentGatewayRequestType::PAYMENT_INITIATE, $response, [
            'status' => $status->value,
            'transactionId' => $transactionId,
        ]);

        return new PaymentResponseDTO(
            transactionDbId: (string) $transaction->id,
            siteReferenceId: $transaction->site_reference_id,
            status: $status,
            transactionId: $transactionId,
            description: 'Simulated payment: '.$status->value,
            amount: $amount,
            pgFees: $pgFees,
            totalAmount: $amount->plus($pgFees),
            transactionDateTime: CarbonImmutable::now(),
            currency: $transaction->currency,
            paymentMethod: $paymentMethod,
            clientName: $transaction->client->name,
            pgConnection: $transaction->pgConnection->name,
            pgResponseRaw: $response,
        );
    }

    public function getTransactionStatus(Transaction $transaction): PaymentResponseDTO
    {
        $transaction->loadMissing(['client', 'pgConnection']);

        $responseData = $transaction->response_data ?? [];

        $this->logSimulatedApiCall(
            $transaction,
            PaymentGatewayRequestType::STATUS_CHECK,
            ['transactionDbId' => $transaction->id],
            $responseData
        );

        return new PaymentResponseDTO(
            transactionDbId: (string) $transaction->id,
            siteReferenceId: $transaction->site_reference_id,
            status: $transaction->status,
            transactionId: (string) ($transaction->transaction_id ?? ''),
            description: 'Simulated status check',
            amount: $transaction->transaction_amount['transaction_amount'],
            pgFees: $transaction->pg_fees['pg_fees'],
            totalAmount: $transaction->total_amount['total_amount'],
            transactionDateTime: $transaction->transaction_date_time
                ? CarbonImmutable::instance($transaction->transaction_date_time)
                : CarbonImmutable::now(),
            currency: $transaction->currency,
            paymentMethod: $transaction->payment_method ?? PaymentMethod::UNKNOWN,
            clientName: $transaction->client->name,
            pgConnection: $transaction->pgConnection->name,
            pgResponseRaw: $responseData,
        );
    }

    public function verifyPayment(Transaction $transaction): PaymentResponseDTO
    {
        return $this->getTransactionStatus($transaction);
    }

    public function isRefundSupported(): bool
    {
        return $this->isRefundSupported;
    }

    public function refundPayment(Transaction $transaction, PaymentRefundDTO $paymentRefundRequest): PaymentResponseDTO
    {
        if (! $this->isRefundSupported) {
            throw new \Exception('Refunds are not supported by this PG Simulator connection.');
        }

        $transaction->loadMissing(['client', 'pgConnection']);

        $requestData = [
            'refundReason' => $paymentRefundRequest->refundReason,
            'refundedAmount' => (string) $paymentRefundRequest->amount->getAmount(),
        ];

        $this->logSimulatedApiCall($transaction, PaymentGatewayRequestType::REFUND, $requestData, [
            'status' => TransactionStatus::REFUNDED->value,
        ]);

        return new PaymentResponseDTO(
            transactionDbId: (string) $transaction->id,
            siteReferenceId: $transaction->site_reference_id,
            status: TransactionStatus::REFUNDED,
            transactionId: (string) ($transaction->transaction_id ?? ''),
            description: 'Simulated refund processed',
            amount: $paymentRefundRequest->amount,
            pgFees: $transaction->pg_fees['pg_fees'],
            totalAmount: $paymentRefundRequest->amount,
            transactionDateTime: CarbonImmutable::now(),
            currency: $paymentRefundRequest->currency,
            paymentMethod: $transaction->payment_method ?? PaymentMethod::UNKNOWN,
            clientName: $transaction->client->name,
            pgConnection: $transaction->pgConnection->name,
            pgResponseRaw: $requestData,
        );
    }
}
