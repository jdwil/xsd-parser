<?php
declare(strict_types=1);

namespace JDWil\Xsd\DOM;

/**
 * Class Schema
 * @package JDWil\Xsd\DOM
 */
/**
 * Class Schema
 * @package JDWil\Xsd\DOM
 */
final class Schema
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var Type[]
     */
    private $types;

    /**
     * @var Attribute[]
     */
    private $attributes;

    private function __construct() {}

    /**
     * @param string $namespace
     * @return Schema
     */
    public static function forNamespace(string $namespace): Schema
    {
        $ret = new Schema();
        $ret->namespace = $namespace;
        $ret->types = [];
        $ret->attributes = [];

        return $ret;
    }

    /**
     * @param \DOMElement $node
     * @return string
     */
    public static function determineNamespace(\DOMElement $node): string
    {
        if ($node->hasAttribute('targetNamespace')) {
            return $node->getAttribute('targetNamespace');
        }
        return $node->namespaceURI ?? 'UNKNOWN';
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param Type $type
     */
    public function addType(Type $type)
    {
        $this->types[] = $type;
    }

    /**
     * @param string $name
     * @return Type|null
     */
    public function findType(string $name)
    {
        foreach ($this->types as $type) {
            if ($type->getName() === $name) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @param Attribute $attribute
     */
    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;
    }

    /**
     * @param string $name
     * @return Attribute|null
     */
    public function findAttribute(string $name)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $name) {
                return $attribute;
            }
        }

        return null;
    }
}
