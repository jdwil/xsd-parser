<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\Documentation;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;
use JDWil\Xsd\Event\FoundDocumentationEvent;

/**
 * Class FoundDocumentationListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundDocumentationListener extends AbstractNormalizerListener implements EventListenerInterface
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundDocumentationEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     * @throws \ReflectionException
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [Documentation::class, 'fromElement']);
    }
}
