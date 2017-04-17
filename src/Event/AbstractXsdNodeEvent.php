<?php
declare(strict_types=1);

namespace JDWil\Xsd\Event;

use JDWil\Xsd\DOM\Definition;

/**
 * Class AbstractXsdNodeEvent
 * @package JDWil\Xsd\Event
 */
abstract class AbstractXsdNodeEvent implements EventInterface
{
    /**
     * @var \DOMElement
     */
    protected $node;

    /**
     * @var Definition
     */
    protected $definition;

    /**
     * @var bool
     */
    protected $propagationStopped;

    /**
     * AbstractXsdNodeEvent constructor.
     * @param \DOMElement $node
     * @param Definition $definition
     */
    public function __construct(\DOMElement $node, Definition $definition)
    {
        $this->propagationStopped = false;
        $this->node = $node;
        $this->definition = $definition;
    }

    /**
     * @return \DOMElement
     */
    public function getNode(): \DOMElement
    {
        return $this->node;
    }

    /**
     * @return Definition
     */
    public function getDefinition(): Definition
    {
        return $this->definition;
    }

    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    /**
     * @return bool
     */
    public function propagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
