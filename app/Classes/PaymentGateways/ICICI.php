<?php

namespace App\Classes\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTO\PaymentRequestDTO;
use App\DTO\PaymentResponseDTO;
use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Carbon\Carbon;
use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ICICI implements PaymentGatewayInterface
{
    protected ?string $merchant_id;

    protected ?string $aggregator_id;

    protected ?string $encryption_key;

    protected ?string $sub_merchant_id;

    protected ?string $paymode;

    protected ?string $default_base_url;

    const PAYMENT_ENDPOINT = '/pg/api/v2/initiateSale';

    const STATUS_CHECK_ENDPOINT = '/pg/api/command';

    const REFUND_ENDPOINT = '/pg/api/command';

    const SETTLEMENT_DETAILS_ENDPOINT = '/pg/api/settlementDetails';

    public function __construct(array $pg_data)
    {
        $this->merchant_id = $pg_data['merchant_id'] ?? null;
        $this->aggregator_id = $pg_data['aggregator_id'] ?? null;
        $this->encryption_key = $pg_data['encryption_key'] ?? null;
        $this->default_base_url = $pg_data['default_base_url'] ?? null;
        $this->sub_merchant_id = $pg_data['sub_merchant_id'] ?? null;
        $this->paymode = $pg_data['paymode'] ?? null;
    }

    protected function getCurrencyCode($currency): string
    {
        if ($currency == 'INR') {
            return '356';
        }

        throw new \Exception('Invalid currency code.');
    }

    public function constructFromPaymentRequest(PaymentRequestDTO $paymentRequest, Transaction $transaction): array
    {
        $request_data = [
            'merchantId' => $this->merchant_id,
            'aggregatorID' => $this->aggregator_id,
            'merchantTxnNo' => $transaction->id.'-'.$paymentRequest->site_reference_id,
            'amount' => (string) $paymentRequest->amount->getAmount(),
            'currencyCode' => $this->getCurrencyCode($paymentRequest->currency),
            'payType' => '0',
            'customerEmailID' => $paymentRequest->customer['email'],
            'transactionType' => 'SALE',
            'txnDate' => Carbon::now()->format('YmdHis'),
            'returnURL' => route('handlePaymentResponse', ['pgClass' => 'ICICI']),
            'customerMobileNo' => $paymentRequest->customer['mobile'],
            'addlParam1' => (string) $transaction->id,
            'addlParam2' => $paymentRequest->site_reference_id,
        ];

        ksort($request_data);

        return $request_data;
    }

    protected function getConcatenatedRequestString($requestData): string
    {
        // assumes that request data is already sorted alphabetically by key
        $concatenatedRequestString = '';
        foreach ($requestData as $key => $value) {
            $concatenatedRequestString .= $value;
        }

        return $concatenatedRequestString;
    }

    protected function getHMacDigest($requestData): string
    {
        return hash_hmac('sha256', $this->getConcatenatedRequestString($requestData), $this->encryption_key);
    }

    protected function getPaymentEndpoint(): string
    {
        return $this->default_base_url.self::PAYMENT_ENDPOINT;
    }

    protected function getTransactionStatusEndpoint(): string
    {
        return $this->default_base_url.self::STATUS_CHECK_ENDPOINT;
    }

    public function getPaymentUrl(PaymentRequestDTO $paymentRequest, Transaction $transaction)
    {
        $requestObject = $this->constructFromPaymentRequest($paymentRequest, $transaction);
        $requestObject['secureHash'] = $this->getHMacDigest($requestObject);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->post($this->getPaymentEndpoint(), $requestObject);

        if ($response->failed()) {
            throw new \Exception('Payment Gateway Error: '.json_encode($response->body()));
        }
        $result = $response->json();

        if ($result['responseCode'] !== 'R1000') {
            throw new \Exception('Payment Gateway Error: '.json_encode($result));
        }

        return $result['redirectURI'].'?tranCtx='.$result['tranCtx'];
    }

    protected function mapResponseToPaymentResponseDTO($response): PaymentResponseDTO
    {
        // ICICI only supports INR currency, so hardcoding it here
        $amount = Money::of($response['amount'], 'INR');
        $pgFees = Money::of($response['oth_charge'], 'INR');

        $dbTransaction = Transaction::with(['client', 'pgConnection'])->find($response['addlParam1']);

        if (! $dbTransaction) {
            throw new \Exception('Transaction not found.');
        }

        $transaction = [
            'transactionDbId' => $response['addlParam1'],
            'siteReferenceId' => $response['addlParam2'],
            'status' => $response['responseCode'] === '0000' ? TransactionStatus::SUCCESS : TransactionStatus::FAILED,
            'transactionId' => $response['txnID'],
            'description' => $response['respDescription'],
            'amount' => $amount,
            'pgFees' => $pgFees,
            'totalAmount' => $amount->plus($pgFees),
            'transactionDateTime' => Carbon::createFromFormat('YmdHis', $response['paymentDateTime']),
            'currency' => Currency::of('INR'),
            'paymentMethod' => $this->mapPaymentModes($response['paymentMode']),
            'clientName' => $dbTransaction->client->name,
            'pgConnection' => $dbTransaction->pgConnection->name,
            'pgResponseRaw' => $response,
        ];

        return new PaymentResponseDTO(...$transaction);
    }

    protected function mapPaymentModes($paymentMode): PaymentMethod
    {
        return match ($paymentMode) {
            'CARD' => PaymentMethod::CARD,
            'NB' => PaymentMethod::NETBANKING,
            'WALLET' => PaymentMethod::WALLET,
            'UPI' => PaymentMethod::UPI,
            'debit_card' => PaymentMethod::UNKNOWN
        };
    }

    public function handlePaymentResponse($response): PaymentResponseDTO
    {
        return $this->mapResponseToPaymentResponseDTO($response);
    }

    public function constructTransactionStatusRequest(Transaction $transaction): array
    {
        $request_data = [
            'merchantId' => $this->merchant_id,
            'aggregatorID' => $this->aggregator_id,
            'merchantTxnNo' => $transaction->id.'-'.$transaction->site_reference_id,
            'originalTxnNo' => $transaction->id.'-'.$transaction->site_reference_id,
            'transactionType' => 'SALE',
        ];

        ksort($request_data);

        return $request_data;
    }

    public function getTransactionStatus(Transaction $transaction): PaymentResponseDTO
    {
        $requestObject = $this->constructTransactionStatusRequest($transaction);
        $requestObject['secureHash'] = $this->getHMacDigest($requestObject);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->post($this->getTransactionStatusEndpoint(), $requestObject);

        if ($response->failed()) {
            throw new \Exception('Payment Gateway Error: '.json_encode($response->body()));
        }
        $result = $response->json();

        if ($result['responseCode'] !== 'R1000') {
            throw new \Exception('Payment Gateway Error: '.json_encode($result));
        }

        return $this->mapResponseToPaymentResponseDTO($result);
    }
}
