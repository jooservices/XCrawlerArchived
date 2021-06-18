<?php

namespace App\Core\EventSourcing;

use App\Models\Event;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EventManager
{
    protected Model $instance;

    protected string $event;

    protected string $category;

    protected array $attributes = [];

    /**
     * Set the event
     *
     * @param string $event
     *
     * @return EventManager
     */
    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Set the model
     *
     * @param Model $instance
     *
     * @return EventManager
     */
    public function setInstance(Model $instance): self
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Set the category
     *
     * @param string $category
     *
     * @return EventManager
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Set the attributes
     *
     * @param array $attributes
     *
     * @return EventManager
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get the globally available request of the container
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    public function request()
    {
        return Container::getInstance()->make('request');
    }

    /**
     * Append other attributes
     *
     * @param array $appends
     *
     * @return EventManager
     */
    public function append(array $appends): self
    {
        $this->attributes = array_merge($this->attributes, $appends);

        return $this;
    }

    /**
     * Store the Event
     *
     * @return Event
     * @throws BindingResolutionException
     */
    public function store(): Event
    {
        $request = $this->request();

        return $this->instance->events()->create([
            'category' => $this->category,
            'event' => $this->event,
            'data' => $this->attributes,
            'ip_address' => $request->getClientIp(),

        ]);
    }

    /**
     * Generate a collection of readable events
     *
     * @return Collection
     */
    public function present(): Collection
    {
        return $this->instance->events->transform(function ($event) {
            return $event->present();
        });
    }

    /**
     * Check if event already exist
     *
     * @return bool
     */
    public function checkExist(): bool
    {
        $model = $this->instance;
        $event = $this->event;

        return Event::forEvent($event)
            ->where('model_type', $model->getMorphClass())->where('model_id', $model->id)
            ->exists();
    }
}
