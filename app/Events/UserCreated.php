<?php

namespace App\Events;

use App\Core\EventSourcing\RecordedEventInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserCreated implements RecordedEventInterface
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return 'user';
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getAggregate(): Model
    {
        return $this->user;
    }
}
