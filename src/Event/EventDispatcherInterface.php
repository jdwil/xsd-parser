<?php
declare(strict_types=1);

namespace JDWil\Xsd\Event;

/**
 * Interface EventDispatcherInterface
 * @package JDWil\Xsd\Event
 */
interface EventDispatcherInterface
{
    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function dispatch(EventInterface $event);

    /**
     * @param EventListenerInterface $eventListener
     * @return mixed
     */
    public function registerListener(EventListenerInterface $eventListener);
}
