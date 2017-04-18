<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundMaxInclusiveEvent;
use JDWil\Xsd\Facet\MaxInclusive;

/**
 * Class FoundMaxInclusiveListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundMaxInclusiveListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundMaxInclusiveEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, MaxInclusive::class);
    }
}