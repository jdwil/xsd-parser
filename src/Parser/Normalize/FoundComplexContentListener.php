<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\ComplexContent;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundComplexContentEvent;

/**
 * Class FoundComplexContentListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundComplexContentListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundComplexContentEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [ComplexContent::class, 'fromElement']);
    }
}
