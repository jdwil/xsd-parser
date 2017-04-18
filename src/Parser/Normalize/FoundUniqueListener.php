<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\Unique;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;
use JDWil\Xsd\Event\FoundUniqueEvent;

/**
 * Class FoundUniqueListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundUniqueListener extends AbstractNormalizerListener implements EventListenerInterface
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundUniqueEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     * @throws \ReflectionException
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [Unique::class, 'fromElement']);
    }
}
