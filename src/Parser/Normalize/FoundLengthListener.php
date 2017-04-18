<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundLengthEvent;
use JDWil\Xsd\Facet\Length;

/**
 * Class FoundLengthListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundLengthListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundLengthEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addFacet($event, Length::class);
    }
}