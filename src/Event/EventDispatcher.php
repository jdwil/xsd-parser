<?php
declare(strict_types=1);

namespace JDWil\Xsd\Event;

/**
 * Class EventDispatcher
 * @package JDWil\Xsd\Event
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var EventListenerInterface[]
     */
    private $listeners;

    public function __construct()
    {
        $this->listeners = [];
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function dispatch(EventInterface $event)
    {
        foreach ($this->listeners as $listener) {
            if ($listener->canHandle($event)) {
                $listener->handle($event);
                if ($event->propagationStopped()) {
                    return;
                }
            }
        }
    }

    /**
     * @param EventListenerInterface $eventListener
     * @return mixed
     */
    public function registerListener(EventListenerInterface $eventListener)
    {
        $this->listeners[] = $eventListener;
    }
}
