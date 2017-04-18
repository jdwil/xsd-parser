<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Element\Schema;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;
use JDWil\Xsd\Event\FoundSchemaEvent;

/**
 * Class FoundSchemaListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundSchemaListener implements EventListenerInterface
{
    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundSchemaEvent;
    }

    /**
     * @param EventInterface $event
     * @return void
     * @throws \ReflectionException
     */
    public function handle(EventInterface $event)
    {
        $node = $event->getNode();
        $definition = $event->getDefinition();

        if ($definition->findElementByNode($node)) {
            return;
        }

        /** @var Schema $schema */
        $schema = Schema::fromElement($node);

        /**
         * DOMDocument can't get the namespaces, so use XPath.
         */
        $xpath = new \DOMXPath($node->ownerDocument);
        foreach ($xpath->query('namespace::*') as $nsNode) {
            /** @var \DOMNameSpaceNode $nsNode */
            $schema->addNamespace($nsNode->localName, $nsNode->nodeValue);
        }

        $schema->setNode($node);
        $definition->addElement($schema);
    }
}
