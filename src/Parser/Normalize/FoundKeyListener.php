<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\Key;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;
use JDWil\Xsd\Event\FoundKeyEvent;

/**
 * Class FoundKeyListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundKeyListener extends AbstractNormalizerListener implements EventListenerInterface
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundKeyEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     * @throws \ReflectionException
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [Key::class, 'fromElement']);
    }
}
