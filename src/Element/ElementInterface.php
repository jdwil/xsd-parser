<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Interface ElementInterface
 * @package JDWil\Xsd\Element
 */
interface ElementInterface
{
    /**
     * @param \DOMElement $node
     * @return ElementInterface
     */
    public static function fromElement(\DOMElement $node): ElementInterface;

    /**
     * @return array
     */
    public function getAttributes(): array;

    /**
     * @param string $key
     * @param string $value
     */
    public function addAttribute(string $key, string $value);

    /**
     * @param ElementInterface $parent
     */
    public function setParent(ElementInterface $parent);

    /**
     * @return ElementInterface
     */
    public function getParent(): ElementInterface;

    /**
     * @param ElementInterface $element
     */
    public function addChildElement(ElementInterface $element);

    /**
     * @return array
     */
    public function getChildren(): array;

    /**
     * @param \DOMNode $node
     */
    public function setNode(\DOMNode $node);

    /**
     * @return \DOMNode
     */
    public function getNode(): \DOMNode;

    /**
     * @return Schema
     */
    public function getSchema(): Schema;
}
