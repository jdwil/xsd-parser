<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Event\EventDispatcherInterface;
use JDWil\Xsd\Event\EventInterface;
use JDWil\Xsd\Exception\DocumentException;

class Parser
{
    /**
     * @var Definition
     */
    private $definition;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var string[]
     */
    private $importedSchemas;

    /**
     * Parser constructor.
     * @param Definition $definition
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(Definition $definition, EventDispatcherInterface $dispatcher)
    {
        $this->definition = $definition;
        $this->dispatcher = $dispatcher;
        $this->importedSchemas = [];
    }

    /**
     * @param \DOMDocument $document
     * @return Definition
     * @throws \JDWil\Xsd\Exception\DocumentException
     */
    public function parse(\DOMDocument $document): Definition
    {
        $this->processDomElement($document->documentElement);

        return $this->definition;
    }

    /**
     * @param \DOMElement $node
     * @throws \JDWil\Xsd\Exception\DocumentException
     */
    protected function processDomElement(\DOMElement $node)
    {
        $event = sprintf('%s\\Found%sEvent', 'JDWil\\Xsd\\Event', ucwords($node->localName));
        if (class_exists($event)) {
            $this->raise(new $event($node, $this->definition));
        } else {
            /*
            var_dump($node);
            throw new DocumentException(
                sprintf('Encountered unknown tag name: %s', $node->localName)
            );
             */
        }

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                // @todo WHat else do we need to handle here?
                if ($childNode instanceof \DOMElement) {
                    $this->processDomElement($childNode);
                }
            }
        }
    }

    /**
     * @param EventInterface $event
     */
    protected function raise(EventInterface $event)
    {
        $this->dispatcher->dispatch($event);
    }
}
