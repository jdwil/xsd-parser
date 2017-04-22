<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use JDWil\Xsd\Element\Attribute;
use JDWil\Xsd\Element\Element;
use JDWil\Xsd\Util\TypeUtil;

/**
 * Class Property
 * @package JDWil\Xsd\Output\Php
 */
class Property
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $visibility = 'protected';

    /**
     * @var mixed
     */
    public $default;

    /**
     * @var bool
     */
    public $required = false;

    /**
     * @var bool
     */
    public $fixed = false;

    /**
     * @var array
     */
    public $enumerations = [];

    /**
     * @var bool
     */
    public $immutable = false;

    /**
     * @var bool
     */
    public $createGetter = true;

    /**
     * @var bool
     */
    public $includeInConstructor = true;

    /**
     * @var string
     */
    public $choiceGroup;

    /**
     * @var bool
     */
    public $isCollection = false;

    /**
     * @var string
     */
    public $collectionOf;

    /**
     * @var int
     */
    public $collectionMin = 0;

    /**
     * @var string
     */
    public $collectionMax = '1';

    /**
     * @var bool
     */
    public $isAttribute = true;

    /**
     * @param Attribute $attribute
     * @return Property
     */
    public static function fromAttribute(Attribute $attribute): Property
    {
        $type = $attribute->getType();
        if (strpos($type, ':') !== false) {
            $pieces = explode(':', $type);
            $name = array_pop($pieces);
            if ($primitive = TypeUtil::typeToPhpPrimitive($name)) {
                $type = $primitive;
            } else {
                $type = $name;
            }
        }

        $ret = new Property();
        $ret->required = $attribute->getUse() === Attribute::USE_REQUIRED;
        $ret->default = $attribute->getDefault();
        $ret->name = $attribute->getName();
        $ret->type = $type;

        return $ret;
    }

    /**
     * @param Element $element
     * @param string|null $type
     * @param string|null $namespace
     * @param string|null $choiceGroup
     * @return Property
     */
    public static function fromElement(
        Element $element,
        string $type = null,
        string $namespace = null,
        string $choiceGroup = null
    ) {
        $ret = new Property();
        $ret->name = $element->getName();
        $ret->collectionMin = $element->getMinOccurs();
        $ret->collectionMax = $element->getMaxOccurs();
        $ret->type = null === $type ? $element->getType() : $type;
        $ret->isAttribute = false;
        if (null !== $namespace) {
            $ret->namespace = $namespace;
        }

        if (null !== $choiceGroup) {
            $ret->choiceGroup = $choiceGroup;
        }

        if ($ret->collectionMin > 1 || (int) $ret->collectionMax > 1 || $ret->collectionMax === 'unbounded') {
            $ret->isCollection = true;
        }

        return $ret;
    }

    /**
     * @param string $value
     */
    public function addEnumeration(string $value)
    {
        $this->enumerations[] = $value;
    }

    /**
     * @return bool
     */
    public function isPrimitive(): bool
    {
        return TypeUtil::isPrimitive($this->type);
    }
}
