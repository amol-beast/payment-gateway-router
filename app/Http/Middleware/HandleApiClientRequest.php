<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleApiClientRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $clientId = $request->input('clientId');

        if (! $clientId) {
            return response()->json(['error' => 'Client ID missing'], 401);
        }

        $client = Client::where('client_id', $clientId)->where('status', 1)->first();

        if (! $client) {
            return response()->json(['error' => 'Invalid Client'], 401);
        }

        $request->merge(['clientDbId' => $client->id]);

        return $next($request);
    }
}
