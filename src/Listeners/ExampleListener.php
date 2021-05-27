<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Listeners;

use Framework\Baseapp\Events\ExampleEvent;

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
     * @param \Framework\Baseapp\Events\ExampleEvent $event
     */
    public function handle(ExampleEvent $event)
    {
    }
}
