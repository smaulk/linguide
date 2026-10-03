<?php

return [
    \App\Infrastructure\Providers\AppServiceProvider::class,
    \App\Infrastructure\Providers\DatabaseServiceProvider::class,
    \App\Infrastructure\Providers\ScheduleServiceProvider::class,
    \App\Infrastructure\Providers\RouteServiceProvider::class,
    \App\Infrastructure\Providers\SourceServiceProvider::class,
    \App\Infrastructure\Providers\WriterServiceProvider::class,
    \App\Infrastructure\Providers\NotificationServiceProvider::class,
    \App\Infrastructure\Providers\TelegramServiceProvider::class,
];
