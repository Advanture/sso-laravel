<?php

namespace App\Listeners;

use App\Events\UserAuthorizedEvent;
use App\Services\NatsService;

class UserAuthorizedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly NatsService $natsService)
    {}

    /**
     * Handle the event.
     */
    public function handle(UserAuthorizedEvent $registeredEvent): void
    {
        $this->natsService->publish('user.authenticated', [
            'user_id' => $registeredEvent->user->id,
            'email' => $registeredEvent->user->email,
        ]);
    }
}
