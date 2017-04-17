<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\DOM\Schema;
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
     * @return mixed
     */
    public function handle(EventInterface $event)
    {
        $node = $event->getNode();
        $definition = $event->getDefinition();

        $namespace = Schema::determineNamespace($node);
        $schema = new Schema($namespace, $node);
        $definition->addSchema($schema);
    }
}
