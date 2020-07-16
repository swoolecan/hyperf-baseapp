<?php
declare(strict_types = 1);

namespace Swoolecan\Baseapp\Listeners;

use Swoolecan\Baseapp\Events\ExampleEvent;

class ExampleListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param \Swoolecan\Baseapp\Events\ExampleEvent $event
     */
    public function handle(ExampleEvent $event)
    {
    }
}
