<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\ElementInterface;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;

/**
 * Class AbstractNormalizerListener
 * @package JDWil\Xsd\Parser\Normalize
 */
abstract class AbstractNormalizerListener implements EventListenerInterface
{
    /**
     * @param EventInterface $event
     * @param callable $typeGenerator
     */
    protected function addNode(EventInterface $event, callable $typeGenerator)
    {
        $node = $event->getNode();
        $definition = $event->getDefinition();
        /** @var ElementInterface $type */
        $type = $typeGenerator($node);
        $type->setNode($node);
        $definition->addElement($type);
        $parent = $definition->findElementByNode($node->parentNode);
        if (!$parent) {
            echo $node->parentNode->localName . "\n";
        }
        $parent->addChildElement($type);
        $type->setParent($parent);
    }
}
