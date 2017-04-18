<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\Choice;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundChoiceEvent;

/**
 * Class FoundChoiceEventListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundChoiceEventListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundChoiceEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [Choice::class, 'fromElement']);
    }
}