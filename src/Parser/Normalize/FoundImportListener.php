<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser\Normalize;

use JDWil\Xsd\Event\EventDispatcherInterface;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Event\EventListenerInterface;
use JDWil\Xsd\Event\FoundImportEvent;
use JDWil\Xsd\Exception\DocumentException;
use JDWil\Xsd\Parser\Parser;

/**
 * Class FoundImportListener
 * @package JDWil\Xsd\Parser\Normalize
 */
class FoundImportListener implements EventListenerInterface
{
    /**
     * @var string[]
     */
    private $importedSchemas;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * FoundImportListener constructor.
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->importedSchemas = [];
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canHandle(EventInterface $event): bool
    {
        return $event instanceof FoundImportEvent;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     * @throws \JDWil\Xsd\Exception\DocumentException
     */
    public function handle(EventInterface $event)
    {
        $node = $event->getNode();
        $definition = $event->getDefinition();

        $location = $node->getAttribute('schemaLocation');

        if (in_array($location, $this->importedSchemas, true)) {
            return;
        }

        $baseUri = $node->baseURI;
        $pieces = explode('/', $baseUri);
        array_pop($pieces);
        $pieces[] = $location;
        $uri = implode('/', $pieces);

        $document = new \DOMDocument('1.0', 'UTF-8');
        if (!$document->load($uri)) {
            throw new DocumentException(
                sprintf('Could not load schema file: %s', $uri)
            );
        }
        $this->importedSchemas[] = $location;

        $parser = new Parser($definition, $this->dispatcher);
        $parser->parse($document);
    }
}
