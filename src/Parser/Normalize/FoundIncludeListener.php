<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\XInclude;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;
use JDWil\Xsd\Event\FoundIncludeEvent;

/**
 * Class FoundXIncludeListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundIncludeListener extends AbstractNormalizerListener implements EventListenerInterface
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundIncludeEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     * @throws \ReflectionException
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [XInclude::class, 'fromElement']);
    }
}
