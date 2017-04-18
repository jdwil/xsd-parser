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
     * @var array
     */
    private static $REFLECTION_CACHE = [];

    /**
     * @var string[]
     */
    protected $attributes = [];

    /**
     * @var ElementInterface[]
     */
    protected $childElements = [];

    /**
     * @var \DOMNode
     */
    protected $node;

    /**
     * @var ElementInterface
     */
    protected $parent;

    /**
     * @param \DOMElement $node
     * @return ElementInterface
     * @throws \ReflectionException
     */
    public static function fromElement(\DOMElement $node): ElementInterface
    {
        $thisName = get_called_class();
        if (isset(self::$REFLECTION_CACHE[$thisName])) {
            $reflectionClass = self::$REFLECTION_CACHE[$thisName]['class'];
            $parameters = self::$REFLECTION_CACHE[$thisName]['parameters'];
            $parameterTypes = self::$REFLECTION_CACHE[$thisName]['parameterTypes'];
        } else {
            $reflectionClass = new \ReflectionClass($thisName);
            $reflectionParameters = $reflectionClass->getConstructor()->getParameters();

            $parameters = [];
            $parameterTypes = [];
            foreach ($reflectionParameters as $parameter) {
                if ($parameter->isDefaultValueAvailable()) {
                    $parameters[$parameter->getName()] = $parameter->getDefaultValue();
                } else {
                    $parameters[$parameter->getName()] = null;
                }
                $parameterTypes[$parameter->getName()] = $parameter->getType();
            }

            self::$REFLECTION_CACHE[$thisName] = [
                'class' => $reflectionClass,
                'parameters' => $parameters,
                'parameterTypes' => $parameterTypes
            ];
        }

        $nonStandardAttributes = [];
        foreach ($node->attributes as $attribute) {
            if (array_key_exists($attribute->name, $parameters)) {
                $value = $attribute->value;
                settype($value, (string) $parameterTypes[$attribute->name]);
                $parameters[$attribute->name] = $value;
            } else {
                $nonStandardAttributes[$attribute->name] = $attribute->value;
            }
        }

        $ret = $reflectionClass->newInstanceArgs($parameters);
        foreach ($nonStandardAttributes as $key => $value) {
            /** @var AbstractElement $ret */
            $ret->addAttribute($key, $value);
        }

        return $ret;
    }

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

    /**
     * @param ElementInterface $parent
     */
    public function setParent(ElementInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return ElementInterface
     */
    public function getParent(): ElementInterface
    {
        return $this->parent;
    }

    /**
     * @param ElementInterface $element
     */
    public function addChildElement(ElementInterface $element)
    {
        $this->childElements[] = $element;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->childElements;
    }
}
