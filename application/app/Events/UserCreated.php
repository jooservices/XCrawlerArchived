<?php

namespace App\Events;

use App\Core\EventSourcing\RecordedEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserCreated implements RecordedEvent
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getCategory(): string
    {
        return 'user';
    }

    public function getPayload(): array
    {
        return [];
    }

    public function getAggregate(): Model
    {
        return $this->user;
    }
}
