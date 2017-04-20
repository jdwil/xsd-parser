<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\Restriction;
use JDWil\Xsd\Element\SimpleType;
use JDWil\Xsd\Element\Union;
use JDWil\Xsd\Element\XList;
use JDWil\Xsd\Facet\Enumeration;
use JDWil\Xsd\Facet\FacetInterface;
use JDWil\Xsd\Facet\FractionDigits;
use JDWil\Xsd\Facet\Length;
use JDWil\Xsd\Facet\MaxExclusive;
use JDWil\Xsd\Facet\MaxInclusive;
use JDWil\Xsd\Facet\MaxLength;
use JDWil\Xsd\Facet\MinExclusive;
use JDWil\Xsd\Facet\MinInclusive;
use JDWil\Xsd\Facet\MinLength;
use JDWil\Xsd\Facet\Pattern;
use JDWil\Xsd\Facet\TotalDigits;
use JDWil\Xsd\Facet\WhiteSpace;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\Argument;
use JDWil\Xsd\Output\Php\ClassBuilder;
use JDWil\Xsd\Output\Php\Method;
use JDWil\Xsd\Output\Php\Property;
use JDWil\Xsd\Util\TypeUtil;

/**
 * Class SimpleTypeProcessor
 * @package JDWil\Xsd\Output\Php\Processor
 */
final class SimpleTypeProcessor extends AbstractProcessor
{
    /**
     * @var SimpleType
     */
    protected $type;

    /**
     * @var Property
     */
    protected $classProperty;

    /**
     * SimpleTypeProcessor constructor.
     * @param SimpleType $element
     * @param Options $options
     * @param Definition $definition
     */
    public function __construct(SimpleType $element, Options $options, Definition $definition)
    {
        $this->type = $element;
        parent::__construct($options, $definition);
    }

    /**
     * @return ClassBuilder
     */
    public function buildClass(): ClassBuilder
    {
        $this->classProperty = new Property();
        $this->classProperty->name = 'value';
        $this->classProperty->type = 'string';
        $this->classProperty->required = true;
        $this->classProperty->immutable = true;

        $this->class->setSimpleType(true);
        $this->class->setNamespace(sprintf('%s\\SimpleType', $this->options->namespacePrefix));
        $this->class->setClassName($this->type->getName());
        $this->initializeClass();
        $this->processClassAttributes();
        $this->class->addProperty($this->classProperty);

        return $this->class;
    }

    protected function processClassAttributes()
    {
        foreach ($this->type->getChildren() as $child) {
            if ($child instanceof Restriction) {
                $this->processRestriction($child);
            } else if ($child instanceof XList) {
                $this->processList($child);
            } else if ($child instanceof Union) {
                $this->processUnion($child);
            }
        }
    }

    /**
     * @param Restriction $restriction
     */
    protected function processRestriction(Restriction $restriction)
    {
        $this->class->uses(sprintf('use %s\\Exception\\ValidationException;', $this->options->namespacePrefix));

        /** @var FacetInterface $facet */
        foreach ($restriction->getFacets() as $facet) {
            switch (get_class($facet)) {
                case MinExclusive::class:
                    $this->class->setMinValue((int) $facet->getValue() + 1);
                    $this->classProperty->type = 'int';
                    break;

                case MinInclusive::class:
                    $this->class->setMinValue((int) $facet->getValue());
                    $this->classProperty->type = 'int';
                    break;

                case MaxExclusive::class:
                    $this->class->setMaxValue((int) $facet->getValue() - 1);
                    $this->classProperty->type = 'int';
                    break;

                case MaxInclusive::class:
                    $this->class->setMaxValue((int) $facet->getValue());
                    $this->classProperty->type = 'int';
                    break;

                case TotalDigits::class:
                    $this->class->setTotalDigits((int) $facet->getValue());
                    $this->classProperty->type = 'int';
                    break;

                case FractionDigits::class:
                    $this->class->setFractionDigits((int) $facet->getValue());
                    $this->classProperty->type = 'float';
                    break;

                case Length::class:
                    $this->class->setValueLength((int) $facet->getValue());
                    break;

                case MinLength::class:
                    $this->class->setValueMinLength((int) $facet->getValue());
                    break;

                case MaxLength::class:
                    $this->class->setValueMaxLength((int) $facet->getValue());
                    break;

                case Enumeration::class:
                    $this->class->addEnumeration($facet->getValue());
                    $this->class->addConstant(sprintf('VALUE_%s', strtoupper($facet->getValue())), $facet->getValue());
                    break;

                case WhiteSpace::class:
                    $this->class->setWhiteSpace($facet->getValue());
                    break;

                case Pattern::class:
                    $this->class->setValuePattern($facet->getValue());
                    break;
            }
        }

        foreach ($restriction->getChildren() as $child) {
            if ($child instanceof SimpleType) {
                // @todo figure this out
                throw new \Exception('dont know what to do here');
            }
        }
    }

    /**
     * @param XList $list
     * @throws \Exception
     */
    protected function processList(XList $list)
    {
        if ($type = $list->getItemType()) {
            list($ns, $typeName) = $this->definition->determineNamespace($type, $list);
            if (!$primitive = TypeUtil::typeToPhpPrimitive($typeName)) {
                $typeNs = $this->getTypeNamespace($typeName, $ns);
                $this->class->uses(sprintf('use %s\\%s\\%s', $this->options->namespacePrefix, $typeNs, $typeName));
            } else {
                $typeName = $primitive;
            }

            $property = new Property();
            $property->name = 'items';
            $property->type = $typeName;
            $property->immutable = true;
            $property->createGetter = false;
            $property->fixed = true;
            $this->class->addProperty($property);
        } else {
            foreach ($list->getChildren() as $child) {
                if ($child instanceof SimpleType) {
                    $type = $child;
                    break;
                }
            }
            // @todo implement this
            throw new \Exception('simpleTypes nested in lists are not implemented yet.');
        }

        $method = new Method();
        $method->name = 'add';
        $method->addArgument(new Argument('item', $typeName));
        $body = <<<_BODY_
        \$this->items[] = \$item;
_BODY_;
        $method->body = $body;
        $this->class->addMethod($method);

        $method = new Method();
        $method->name = 'all';
        $method->returns = 'array';
        $body = <<<_BODY_
        return \$this->items;
_BODY_;
        $method->body = $body;
        $this->class->addMethod($method);
    }

    protected function processUnion(Union $union)
    {
        /** @var SimpleType $parent */
        $parent = $union->getParent();
        $types = $parent->getType();
        $this->normalizeTypes($types);
    }
}
