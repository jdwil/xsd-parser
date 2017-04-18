<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\Extension;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundExtensionEvent;

/**
 * Class FoundExtensionListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundExtensionListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundExtensionEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [Extension::class, 'fromElement']);
    }
}
