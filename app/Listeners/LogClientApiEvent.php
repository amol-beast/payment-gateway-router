<?php

namespace App\Listeners;

use App\Events\ClientApiEvent;
use App\Models\ClientApiLog;

class LogClientApiEvent
{
    public function handle(ClientApiEvent $event): void
    {
        ClientApiLog::create([
            'client_id' => $event->clientId,
            'event' => $event->event,
            'request_data' => $event->requestData,
            'response_data' => $event->responseData,
            'source_ip' => $event->sourceIp,
            'result' => $event->result,
            'datetime' => now(),
        ]);
    }
}
