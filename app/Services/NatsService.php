<?php

namespace App\Services;

use Basis\Nats\Client;
use Basis\Nats\Configuration;
use Basis\Nats\Message\Payload;

class NatsService
{
    private Client $client;

    public function __construct()
    {
        $configuration = new Configuration(
            host: config('nats.host'),
            port: config('nats.port'),
            nkey: config('nats.nkey'),
            timeout: 5,
        );

        $this->client = new Client($configuration);
    }

    public function publish(string $eventName, array $payload, string $streamName = 'backend', string $subject = 'backend.topic1'): void
    {
        $stream = $this->client->getApi()->getStream($streamName);

        $payload = new Payload(
            json_encode($payload),
            ['event' => $eventName],
        );

        $stream->publish($subject, $payload);
    }
}
