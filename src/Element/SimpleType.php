<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;
use JDWil\Xsd\Util\TypeUtil;

/**
 * Class SimpleType
 * @package JDWil\Xsd\Element
 */
class SimpleType extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $name;

    /**
     * SimpleType constructor.
     * @param string|null $id
     * @param string|null $name
     */
    public function __construct(string $id = null, string $name = null)
    {
        $this->name = $name;
        parent::__construct($id);
    }

    /**
     * If type can be represented with a primitive, it returns it.
     * Returns null otherwise.
     *
     * @return null|string
     */
    public function canBeMappedToPrimitive()
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof Restriction) {
                $type = $child->getBase();
                return TypeUtil::typeToPhpPrimitive($type);
            }
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function isEnum()
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof Restriction && $child->isEnum()) {
                return $child->getEnumValues();
            }
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $parent = $this->getParent();
        if ($parent instanceof Element) {
            return $parent->getName();
        }

        return null;
    }
}
