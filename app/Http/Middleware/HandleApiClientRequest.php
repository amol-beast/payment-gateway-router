<?php

namespace App\Http\Middleware;

use App\Classes\Encryption;
use App\Enums\ClientApiLogResult;
use App\Events\ClientApiEvent;
use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleApiClientRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Retrieve the signature provided by the client

        // 2. Fetch the shared secret key from db
        $client = Client::where('client_id', $clientId)->where('status', 1)->first();
        if (! $client) {
            return response()->json(['error' => 'Invalid Client'], 401);
        }

        $secretKey = $client->client_secret;

        $request->merge(['clientDbId' => $client->id]);
        return $next($request);
    }

    /**
     * @param array<string, mixed> $responseData
     * @param array<string, mixed>|null $requestData
     */
    private function logEvent(Request $request, Client $client, ClientApiLogResult $result, array $responseData, ?array $requestData = null): void
    {
        ClientApiEvent::dispatch(
            $client->id,
            $request->route()?->getName() ?? $request->path(),
            $result,
            $requestData ?? [],
            $responseData,
            $request->ip(),
        );
    }
}
