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
use JDWil\Xsd\Output\Php\InterfaceGenerator;
use JDWil\Xsd\Output\Php\Method;
use JDWil\Xsd\Output\Php\Property;
use JDWil\Xsd\Util\NamespaceUtil;
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
     * SimpleTypeProcessor constructor.
     * @param SimpleType $element
     * @param Options $options
     * @param Definition $definition
     * @param InterfaceGenerator $interfaceGenerator
     */
    public function __construct(
        SimpleType $element,
        Options $options,
        Definition $definition,
        InterfaceGenerator $interfaceGenerator
    ) {
        $this->type = $element;
        parent::__construct($options, $definition, $interfaceGenerator);
    }

    /**
     * @return ClassBuilder
     * @throws \JDWil\Xsd\Exception\ValidationException
     */
    public function buildClass()
    {
        $this->initializeValueProperty();

        $this->usesInterface(InterfaceGenerator::TYPE_SIMPLE_TYPE);
        $this->class->setSimpleType(true);
        $this->class->setNamespace(sprintf('%s\\SimpleType', $this->options->namespacePrefix));
        $this->class->setClassName($this->type->getName());
        $this->initializeClass();
        $this->processClassAttributes();

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
     * @param XList $list
     * @throws \Exception
     */
    protected function processList(XList $list)
    {
        if ($type = $list->getItemType()) {
            list($ns, $typeName) = $this->definition->determineNamespace($type, $list);
            if (!$primitive = TypeUtil::typeToPhpPrimitive($typeName)) {
                $typeNs = $this->getTypeNamespace($typeName, $ns);
                $this->class->uses(NamespaceUtil::classNamespace($this->options, $typeNs, $typeName));
            } else {
                $typeName = $primitive;
            }

            $property = new Property();
            $property->name = 'items';
            $property->type = $typeName;
            $property->immutable = true;
            $property->createGetter = false;
            $property->isCollection = true;
            $property->collectionOf = $typeName;
            $this->class->resetProperties();
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
    }

    /**
     * @param Union $union
     */
    protected function processUnion(Union $union)
    {
        /** @var SimpleType $parent */
        $parent = $union->getParent();
        $types = $parent->getType();
        if (!is_array($types)) {
            $types = [$types];
        }
        $types = $this->normalizeTypes($types);
        $statements = [];
        $namespaces = [];
        foreach ($types as $type => $ns) {
            if ($primitive = TypeUtil::typeToPhpPrimitive($type)) {
                switch ($primitive) {
                    case 'int':
                        $statements[] = "!preg_match(/'\\d+'/, \$value)";
                        break;
                    case 'bool':
                        $statements[] = "!preg_match('/true|false|0|1/', \$value)";
                        break;
                    case 'float':
                        $statements[] = "!preg_match('/-?\\d*\\.\\d+/', \$value)";
                        break;
                }
            } else {
                $statements[] = sprintf('!$value instanceof %s', $type);
                $namespaces[] = sprintf('%s\\%s\\%s', $this->options->namespacePrefix, $ns, $type);
            }
        }

        $this->classProperty->type = null;
        $this->usesValidationException();
        foreach ($namespaces as $namespace) {
            $this->class->uses($namespace);
        }
        $conditions = implode(' && ', $statements);
        $validator = <<<_VALIDATOR_
        if ($conditions) {
            throw new ValidationException('value is not valid for union.');
        }
_VALIDATOR_;
        $this->class->addValidator($validator);
    }
}
