<?php
declare(strict_types=1);

namespace JDWil\Xsd\DOM;

/**
 * Class Type
 * @package JDWil\Xsd\DOM
 */
class Type
{
    const TYPE_UNKNOWN = 'UNKNOWN';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $baseType;

    /**
     * @var Attribute[]
     */
    protected $attributes;

    /**
     * Type constructor.
     * @param string $name
     * @param string $baseType
     */
    public function __construct(string $name, string $baseType)
    {
        $this->name = $name;
        $this->baseType = $baseType;
        $this->attributes = [];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getBaseType(): string
    {
        return $this->baseType;
    }

    /**
     * @param Attribute $attribute
     */
    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
