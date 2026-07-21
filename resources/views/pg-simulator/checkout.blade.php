<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PG Simulator Checkout</title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; border-radius: 8px; padding: 32px; width: 360px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .badge { display: inline-block; background: #fef3c7; color: #92400e; font-size: 12px; padding: 2px 8px; border-radius: 4px; margin-bottom: 16px; }
        .row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: #374151; }
        .row strong { color: #111827; }
        select { width: 100%; padding: 8px; margin: 12px 0; border: 1px solid #d1d5db; border-radius: 4px; }
        button { width: 100%; padding: 10px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; margin-top: 8px; }
        .success { background: #16a34a; color: #fff; }
        .pending { background: #f59e0b; color: #fff; }
        .failed { background: #dc2626; color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">PG Simulator</span>
        <h1>{{ $transaction->pgConnection->name }}</h1>
        <div class="row"><span>Reference</span><strong>{{ $transaction->site_reference_id }}</strong></div>
        <div class="row"><span>Amount</span><strong>{{ $amount->getCurrency()->getCode() }} {{ $amount->getAmount() }}</strong></div>

        <form method="POST" action="{{ $responseUrl }}">
            @csrf
            <input type="hidden" name="transactionDbId" value="{{ $transaction->id }}">
            <input type="hidden" name="transactionId" value="{{ $transactionId }}">
            <input type="hidden" name="pgFees" value="{{ $pgFees->getAmount() }}">

            <select name="paymentMethod">
                <option value="card">Card</option>
                <option value="upi">UPI</option>
                <option value="nettbanking">Netbanking</option>
                <option value="wallet">Wallet</option>
            </select>

            <button type="submit" name="status" value="success" class="success">Simulate Success</button>
            <button type="submit" name="status" value="pending" class="pending">Simulate Pending</button>
            <button type="submit" name="status" value="failed" class="failed">Simulate Failure</button>
        </form>
    </div>
</body>
</html>
