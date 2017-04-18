<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundMinInclusiveEvent;
use JDWil\Xsd\Facet\MinInclusive;

/**
 * Class FoundMinInclusiveListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundMinInclusiveListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundMinInclusiveEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, MinInclusive::class);
    }
}