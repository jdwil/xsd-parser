<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundMinExclusiveEvent;
use JDWil\Xsd\Facet\MinExclusive;

/**
 * Class FoundMinExclusiveListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundMinExclusiveListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundMinExclusiveEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, MinExclusive::class);
    }
}