<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\Appinfo;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\FoundAppinfoEvent;

/**
 * Class FoundAppinfoListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundAppinfoListener extends AbstractNormalizerListener
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundAppinfoEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->addNode($event, [Appinfo::class, 'fromElement']);
    }
}