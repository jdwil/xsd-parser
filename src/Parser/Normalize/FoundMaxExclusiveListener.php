<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundMaxExclusiveEvent;
use JDWil\Xsd\Facet\MaxExclusive;

/**
 * Class FoundMaxExclusiveListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundMaxExclusiveListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundMaxExclusiveEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, MaxExclusive::class);
    }
}