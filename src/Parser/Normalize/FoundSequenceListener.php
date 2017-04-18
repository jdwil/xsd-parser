<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\Sequence;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;
use JDWil\Xsd\Event\FoundSequenceEvent;

/**
 * Class FoundSequenceListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundSequenceListener extends AbstractNormalizerListener implements EventListenerInterface
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundSequenceEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     * @throws \ReflectionException
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [Sequence::class, 'fromElement']);
    }
}
