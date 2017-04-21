<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use JDWil\Xsd\Element\Attribute;
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
        $ret->name = $attribute->getName();
        $ret->type = $type;
        $ret->default = $attribute->getDefault();

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
