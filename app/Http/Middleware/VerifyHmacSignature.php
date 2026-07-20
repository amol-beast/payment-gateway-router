<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHmacSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Retrieve the signature provided by the client
        $clientId = $request->header('X-Client-Id');
        if (! $clientId) {
            return response()->json(['error' => 'Client ID missing'], 401);
        }

        $clientSignature = $request->header('X-Signature');

        if (! $clientSignature) {
            return response()->json(['error' => 'Signature missing'], 401);
        }

        // 2. Fetch the shared secret key from db
        $client = Client::where('client_id', $clientId)->where('status', 1)->first();
        if (! $client) {
            return response()->json(['error' => 'Invalid Client'], 401);
        }

        $secretKey = $client->client_secret;

        // 3. Recompute the signature using the raw request payload
        $payload = $request->getContent();
        $computedSignature = hash_hmac('sha256', $payload, $secretKey);

        // 4. Use hash_equals to prevent timing attacks
        if (! hash_equals($computedSignature, $clientSignature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}
