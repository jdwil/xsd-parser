<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundWhiteSpaceEvent;
use JDWil\Xsd\Facet\WhiteSpace;

/**
 * Class FoundWhiteSpaceListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundWhiteSpaceListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundWhiteSpaceEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, WhiteSpace::class);
    }
}