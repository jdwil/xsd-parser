<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundEnumerationEvent;
use JDWil\Xsd\Facet\Enumeration;

/**
 * Class FoundEnumerationListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundEnumerationListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundEnumerationEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, Enumeration::class);
    }
}