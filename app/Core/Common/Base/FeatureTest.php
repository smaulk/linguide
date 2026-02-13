<?php
declare(strict_types=1);

namespace App\Core\Common\Base;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Event;

abstract class FeatureTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function fakeEventWithModel(): void
    {
        $dispatcher = Event::getFacadeRoot();
        Event::fake();
        Model::setEventDispatcher($dispatcher);
    }
}
