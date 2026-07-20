<x-mail::message>
# Transaction {{ ucfirst($transactionStatus->value) }}

| | |
|---|---|
| Reference ID | {{ $paymentResponseDTO->siteReferenceId }} |
| Transaction ID | {{ $paymentResponseDTO->transactionId }} |
| Status | {{ ucfirst($transactionStatus->value) }} |
| Amount | {{ $paymentResponseDTO->amount->format() }} |
| Date | {{ $paymentResponseDTO->transactionDateTime->toDateTimeString() }} |

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
