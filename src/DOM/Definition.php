<?php
declare(strict_types=1);

namespace JDWil\Xsd\DOM;

use JDWil\Xsd\Element\AbstractElement;
use JDWil\Xsd\Element\ElementInterface;
use JDWil\Xsd\Element\Schema;

/**
 * Class Definition
 * @package JDWil\Xsd\DOM
 */
class Definition
{
    /**
     * @var AbstractElement[]
     */
    private $elements;

    /**
     * Definition constructor.
     */
    public function __construct()
    {
        $this->elements = [];
    }

    /**
     * @param ElementInterface $element
     */
    public function addElement(ElementInterface $element)
    {
        $this->elements[] = $element;
    }

    /**
     * @return array
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param \DOMNode $node
     * @return ElementInterface|null
     */
    public function findElementByNode(\DOMNode $node) {
        foreach ($this->elements as $element) {
            if ($element->getNode()->isSameNode($node)) {
                return $element;
            }
        }

        return null;
    }

    /**
     * @return Schema[]
     */
    public function getSchemas(): array
    {
        return $this->getElementsByType(Schema::class);
    }

    /**
     * @param string $type
     * @return array
     */
    public function getElementsByType(string $type): array
    {
        $ret = [];
        foreach ($this->elements as $element) {
            if (get_class($element) === $type) {
                $ret[] = $element;
            }
        }

        return $ret;
    }

    /**
     * @param string $name
     * @param string|null $namespace
     * @return AbstractElement|mixed|null
     */
    public function findElementByName(string $name, string $namespace = null)
    {
        foreach ($this->getSchemas() as $schema) {
            if (null !== $namespace && $schema->getXmlns() !== $namespace) {
                continue;
            }

            foreach ($schema->getChildren() as $element) {
                if (method_exists($element, 'getName') && $element->getName() === $name) {
                    return $element;
                }
            }
        }

        return null;
    }
}
