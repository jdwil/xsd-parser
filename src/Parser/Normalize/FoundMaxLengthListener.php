<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundMaxLengthEvent;
use JDWil\Xsd\Facet\MaxLength;

/**
 * Class FoundMaxLengthListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundMaxLengthListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundMaxLengthEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, MaxLength::class);
    }
}