<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\ElementInterface;
use JDWil\Xsd\Element\Restriction;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;
use JDWil\Xsd\Facet\FacetInterface;

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

    /**
     * @param EventInterface $event
     * @param string $className
     */
    protected function addFacet(EventInterface $event, string $className)
    {
        $node = $event->getNode();
        $definition = $event->getDefinition();
        /** @var FacetInterface $type */
        $type = new $className($node->getAttribute('value'));
        $parent = $definition->findElementByNode($node->parentNode);
        if ($parent instanceof Restriction) {
            $parent->addFacet($type);
        }
    }
}
