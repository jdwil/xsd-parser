<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundComplexTypeEvent;

/**
 * Class FoundComplexTypeListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundComplexTypeListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundComplexTypeEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [ComplexType::class, 'fromElement']);
    }
}
