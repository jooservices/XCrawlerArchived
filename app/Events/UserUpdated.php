<?php

namespace App\Events;

use App\Core\EventSourcing\RecordedEventInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserUpdated implements RecordedEventInterface
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
