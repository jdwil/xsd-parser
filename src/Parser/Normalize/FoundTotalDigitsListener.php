<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundTotalDigitsEvent;
use JDWil\Xsd\Facet\TotalDigits;

/**
 * Class FoundTotalDigitsListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundTotalDigitsListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundTotalDigitsEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, TotalDigits::class);
    }
}