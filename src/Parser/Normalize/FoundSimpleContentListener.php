<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\SimpleContent;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundSimpleContentEvent;

/**
 * Class FoundSimpleContentListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundSimpleContentListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundSimpleContentEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [SimpleContent::class, 'fromElement']);
    }
}
