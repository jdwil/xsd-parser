<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundMinLengthEvent;
use JDWil\Xsd\Facet\MinLength;

/**
 * Class FoundMinLengthListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundMinLengthListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundMinLengthEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, MinLength::class);
    }
}