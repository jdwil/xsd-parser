<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundFractionDigitsEvent;
use JDWil\Xsd\Facet\FractionDigits;

/**
 * Class FoundFractionDigitsListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundFractionDigitsListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundFractionDigitsEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, FractionDigits::class);
    }
}