<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\AttributeGroup;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundAttributeGroupEvent;

/**
 * Class FoundAttributeGroupListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundAttributeGroupListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundAttributeGroupEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [AttributeGroup::class, 'fromElement']);
    }
}
