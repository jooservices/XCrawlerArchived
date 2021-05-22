<?php

namespace App\Providers;

use App\Notifications\FailedJobNotification;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::failing(function (JobFailed $event) {
            // Not send for testing
            if (!app()->environment('testing')) {
                Notification::route('slack', config('services.slack.exceptions'))->notify(new FailedJobNotification($event));
            }
        });
    }
}
