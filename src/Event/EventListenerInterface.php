<?php
declare(strict_types=1);

namespace JDWil\Xsd\Event;

/**
 * Interface EventListenerInterface
 * @package JDWil\Xsd\Event
 */
interface EventListenerInterface
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool;

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event);
}
