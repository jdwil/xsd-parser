<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;
use JDWil\Xsd\Facet\Enumeration;
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
        $children = $this->getChildren();
        if (count($children) === 1) {
            $child = $children[0];
            if ($child instanceof Restriction) {
                $type = $child->getBase();
                return TypeUtil::typeToPhpPrimitive($type);
            }
        }
    }

    /**
     * @return array|null
     */
    public function isEnum()
    {
        $children = $this->getChildren();
        if ($this->hasChildType(Restriction::class, true) && count($children) === 1) {
            $child = $children[0];
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
        return $this->name;
    }
}
