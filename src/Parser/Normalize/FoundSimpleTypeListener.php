<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\DOM\SimpleType;
use JDWil\Xsd\DOM\Type;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;
use JDWil\Xsd\Event\FoundSimpleTypeEvent;

/**
 * Class FoundSimpleTypeListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundSimpleTypeListener implements EventListenerInterface
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundSimpleTypeEvent;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event)
    {
        $node = $event->getNode();
        $definition = $event->getDefinition();

        $name = $node->getAttribute('name');
        $baseType = Type::TYPE_UNKNOWN;
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                /** @var \DOMElement $childNode */
                if ($childNode->localName === 'restriction') {
                    $baseType = str_replace('xsd:', '', $childNode->getAttribute('base'));
                }
            }
        }

        $definition->getSchemaForNode($node)->addType(new SimpleType($name, $baseType, $node));
    }
}
