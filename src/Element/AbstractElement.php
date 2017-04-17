<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;


/**
 * Class AbstractElement
 * @package JDWil\Xsd\Element
 */
abstract class AbstractElement implements ElementInterface
{
    /**
     * @var string[]
     */
    protected $attributes;

    /**
     * @var \DOMNode
     */
    protected $node;

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addAttribute(string $key, string $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @param \DOMNode $node
     */
    public function setNode(\DOMNode $node)
    {
        $this->node = $node;
    }

    /**
     * @return \DOMNode
     */
    public function getNode(): \DOMNode
    {
        return $this->node;
    }
}