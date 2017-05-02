<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use Doctrine\Common\Inflector\Inflector;
use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\AbstractElement;
use JDWil\Xsd\Element\Annotation;
use JDWil\Xsd\Element\Appinfo;
use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Element\Documentation;
use JDWil\Xsd\Element\ElementInterface;
use JDWil\Xsd\Element\Restriction;
use JDWil\Xsd\Element\SimpleType;
use JDWil\Xsd\Exception\FileSystemException;
use JDWil\Xsd\Exception\TypeNotFoundException;
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
use JDWil\Xsd\Output\Php\AnnotatedObjectInterface;
use JDWil\Xsd\Output\Php\Argument;
use JDWil\Xsd\Output\Php\ClassBuilder;
use JDWil\Xsd\Output\Php\InterfaceGenerator;
use JDWil\Xsd\Output\Php\Method;
use JDWil\Xsd\Output\Php\Property;
use JDWil\Xsd\Stream\OutputStream;
use JDWil\Xsd\Util\NamespaceUtil;
use JDWil\Xsd\Util\TypeUtil;
use JDWil\Xsd\ValueObject\Enum;

abstract class AbstractProcessor implements ProcessorInterface
{
    const XSD_NAMESPACE = 'http://www.w3.org/2001/XMLSchema';

    /**
     * @var ClassBuilder
     */
    protected $class;

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var Definition
     */
    protected $definition;

    /**
     * @var Property
     */
    protected $classProperty;

    /**
     * @var InterfaceGenerator
     */
    protected $interfaceGenerator;

    /**
     * AbstractProcessor constructor.
     * @param Options $options
     * @param Definition $definition
     * @param InterfaceGenerator $interfaceGenerator
     */
    public function __construct(Options $options, Definition $definition, InterfaceGenerator $interfaceGenerator)
    {
        $this->options = $options;
        $this->definition = $definition;
        $this->interfaceGenerator = $interfaceGenerator;
        $this->class = new ClassBuilder($options);
    }

    protected function initializeClass()
    {
        if ($this->options->declareStrictTypes) {
            $this->class->addDeclaration('declare(strict_types=1);');
        }
    }

    protected function initializeValueProperty()
    {
        $this->classProperty = new Property();
        $this->classProperty->name = 'value';
        $this->classProperty->type = 'string';
        $this->classProperty->required = true;
        $this->classProperty->immutable = true;
        $this->class->addProperty($this->classProperty);
    }

    /**
     * @param Restriction $restriction
     * @throws \JDWil\Xsd\Exception\FileSystemException
     * @throws \JDWil\Xsd\Exception\ValidationException
     */
    protected function processRestriction(Restriction $restriction)
    {
        if (null === $this->classProperty) {
            $this->initializeValueProperty();
        }
        $this->usesValidationException();
        $base = $restriction->getBase();
        $type = $classNs = $ns = '';
        $this->analyzeType($base, $type, $classNs, $ns);
        $this->classProperty->type = $type;
        if (null !== $type && !TypeUtil::isPrimitive($type)) {
            $as = null;
            if ($type === $this->class->getClassName()) {
                $as = str_replace(':', '', $base);
            }
            $this->class->uses(NamespaceUtil::classNamespace($this->options, $classNs, $type), $as);
        }

        /** @var FacetInterface $facet */
        foreach ($restriction->getFacets() as $facet) {
            switch (get_class($facet)) {
                case MinExclusive::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_MIN);
                    $value = $this->setPrimitiveType($facet->getValue(), $type, $restriction);
                    $this->class->setMinExclusive($value);
                    break;

                case MinInclusive::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_MIN);
                    $value = $this->setPrimitiveType($facet->getValue(), $type, $restriction);
                    $this->class->setMinInclusive($value);
                    break;

                case MaxExclusive::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_MAX);
                    $value = $this->setPrimitiveType($facet->getValue(), $type, $restriction);
                    $this->class->setMaxExclusive($value);
                    break;

                case MaxInclusive::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_MAX);
                    $value = $this->setPrimitiveType($facet->getValue(), $type, $restriction);
                    $this->class->setMaxInclusive($value);
                    break;

                case TotalDigits::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_LENGTH);
                    $this->class->setTotalDigits((int) $facet->getValue());
                    break;

                case FractionDigits::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_LENGTH);
                    $this->class->setFractionDigits((int) $facet->getValue());
                    break;

                case Length::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_LENGTH);
                    $this->class->setValueLength((int) $facet->getValue());
                    break;

                case MinLength::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_LENGTH);
                    $this->class->setValueMinLength((int) $facet->getValue());
                    break;

                case MaxLength::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_LENGTH);
                    $this->class->setValueMaxLength((int) $facet->getValue());
                    break;

                case Enumeration::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_ENUM);
                    $this->class->addEnumeration($facet->getValue());
                    $this->class->addConstant(sprintf('VALUE_%s', strtoupper($facet->getValue())), $facet->getValue());
                    break;

                case WhiteSpace::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_WHITESPACE);
                    $this->class->setWhiteSpace($facet->getValue());
                    break;

                case Pattern::class:
                    $this->usesInterface(InterfaceGenerator::TYPE_PATTERN);
                    $this->class->setValuePattern($facet->getValue());
                    break;
            }
        }

        foreach ($restriction->getChildren() as $child) {
            if ($child instanceof SimpleType) {
                $this->extendSimpleType($child);
            }
        }
    }

    /**
     * @param $value
     * @param string $type
     * @param AbstractElement $element
     * @return mixed
     */
    protected function setPrimitiveType($value, string $type, AbstractElement $element)
    {
        while (!TypeUtil::isPrimitive($type)) {
            list($namespace, $name) = $this->definition->determineNamespace($type, $element);
            $reference = $this->definition->findElementByName($name, $namespace);

            if (null !== $reference && $newType = $reference->canBeMappedToPrimitive()) {
                $type = $newType;
                $this->classProperty->comparisonType = $type;
            } else {
                break;
            }
        }
        // @todo should we ignore this?
        @settype($value, $type);

        return $value;
    }

    /**
     * @param string $type One of InterfaceGenerator::TYPE_* constants
     * @throws \JDWil\Xsd\Exception\ValidationException
     */
    protected function usesInterface(string $type)
    {
        $this->interfaceGenerator->generateInterface($type);
        $this->class->addImplements($type);
        $this->class->uses(NamespaceUtil::classNamespace(
            $this->options,
            InterfaceGenerator::INTERFACE_NAMESPACE,
            $type
        ));
    }

    /**
     * @param Annotation $annotation
     * @param AnnotatedObjectInterface $object
     * @return string
     */
    protected function processAnnotation(Annotation $annotation, AnnotatedObjectInterface $object): string
    {
        $ret = '';
        foreach ($annotation->getChildren() as $child) {
            if ($child instanceof Appinfo) {
                if ($source = $child->getSource()) {
                    $ret .= sprintf("Source: %s\n", $source);
                }
                $ret .= sprintf("%s\n", $child->getNode()->nodeValue);
            } else if ($child instanceof Documentation) {
                if ($source = $child->getSource()) {
                    $ret .= sprintf("Source: %s\n", $source);
                }
                $ret .= sprintf("%s\n", $child->getNode()->nodeValue);
            }
        }

        $object->setAnnotation($ret);

        return $ret;
    }

    /**
     * @param SimpleType $type
     */
    protected function extendSimpleType(SimpleType $type)
    {
        $processor = new SimpleTypeProcessor($type, $this->options, $this->definition, $this->interfaceGenerator);
        $class = $processor->buildClass();
        foreach ($class->getProperties() as $property) {
            $this->class->addProperty($property);
        }
        foreach ($class->getMethods() as $method) {
            $this->class->addMethod($method);
        }
        foreach ($class->getUses() as $uses) {
            $this->class->uses($uses);
        }
    }

    /**
     * @param string $type
     * @param string $namespace
     * @param int $min
     * @param int $max
     * @throws FileSystemException
     * @returns ClassBuilder
     */
    protected function buildCollection(string $type, string $namespace, int $min, int $max): ClassBuilder
    {
        if (strpos($type, ':') !== false) {
            $pieces = explode(':', $type);
            $type = array_pop($pieces);
        }
        $className = sprintf('%sCollection', $type);
        $builder = new ClassBuilder($this->options);
        $builder
            ->setNamespace(sprintf('%s\\ValueObject', $this->options->namespacePrefix))
            ->setClassName($className)
            ->uses(NamespaceUtil::classNamespace($this->options, 'Exception', 'ValidationException'))
            ->uses(NamespaceUtil::classNamespace($this->options, 'Stream', 'OutputStream'))
            ->uses($namespace)
        ;

        if ($this->options->declareStrictTypes) {
            $builder->addDeclaration('declare(strict_types=1);');
        }

        $property = new Property();
        $property->name = 'items';
        $property->type = 'array';
        $property->default = '[]';
        $property->createGetter = false;
        $property->immutable = true;
        $property->fixed = true;
        $builder->addProperty($property);

        $method = new Method();
        $method->name = 'add';
        $method->addArgument(new Argument('item', $type));
        if ($max > -1) {
            $method->throws('ValidationException');
            $body = <<<_BODY_
        \$this->items[] = \$item;
        if ($max < count(\$this->items)) {
            throw new ValidationException('collection can have at most $max item(s)');
        }
_BODY_;
        } else {
            $body = <<<_BODY_
        \$this->items[] = \$item;
_BODY_;

        }
        $method->body = $body;
        $builder->addMethod($method);

        $method = new Method();
        $method->name = 'all';
        $method->returns = 'array';
        if ($min) {
            $method->throws('ValidationException');
            $body = <<<_BODY_
        if ($min > count(\$this->items)) {
            throw new ValidationException('collection must have at least $min item(s)');
        }
        
        return \$this->items;
_BODY_;

        } else {
            $body = <<<_BODY_
        return \$this->items;
_BODY_;
        }
        $method->body = $body;
        $builder->addMethod($method);

        $method = new Method();
        $method->name = 'writeXML';
        $method->addArgument(new Argument('stream', 'OutputStream'));
        $method->addArgument(new Argument('tagName', 'string'));
        $body = <<<_BODY_
        foreach (\$this->items as \$item) {
            \$item->writeXML(\$stream, \$tagName);
        }
_BODY_;
        $method->body = $body;
        $builder->addMethod($method);


        $dir = sprintf('%s/ValueObject', $this->options->outputDirectory);
        if (!@mkdir($dir) && !is_dir($dir)) {
            throw new FileSystemException(sprintf('Could not create directory %s', $dir));
        }
        $builder->writeTo(OutputStream::streamedTo(sprintf('%s/%s.php', $dir, $className)));

        return $builder;
    }

    /**
     * @todo This needs to support some type of double so we don't try to create these classes during testing.
     *
     * @param string $type
     * @returns bool
     * @throws FileSystemException
     */
    protected function createXsdType(string $type): bool
    {
        $sourceDir = __DIR__ . '/../../../Type';
        $fileTarget = sprintf('%s/%s.php', $sourceDir, $type);
        if (!file_exists($fileTarget)) {
            return false;
        }

        $source = file_get_contents($fileTarget);
        $newNamespace = sprintf('namespace %s;', $this->classNamespace('Xsd'));
        $source = preg_replace('/^namespace [^;]+;/m', $newNamespace, $source);
        $newUseStatement = sprintf('use %s;', $this->validationExceptionNs());
        $source = preg_replace('/^use .*ValidationException;/m', $newUseStatement, $source);

        $outputDir = sprintf('%s/Xsd', $this->options->outputDirectory);
        if (!@mkdir($outputDir) && !is_dir($outputDir)) {
            throw new FileSystemException(sprintf('Could not create directory %s', $outputDir));
        }
        $stream = OutputStream::streamedTo(sprintf('%s/%s.php', $outputDir, $type));
        $stream->write($source);

        if (preg_match('/extends (\w+)/m', $source, $m)) {
            $this->createXsdType($m[1]);
        }

        if (preg_match('/implements (\w+)/m', $source, $m)) {
            $this->createXsdType($m[1]);
        }

        return true;
    }

    /**
     * @param string $type
     * @param string $namespace
     * @return string
     */
    protected function getTypeNamespace(string $type, string $namespace = null): string
    {
        $typeObject = $this->definition->findElementByName($type, $namespace);
        switch (get_class($typeObject)) {
            case SimpleType::class:
                return 'SimpleType';
            case ComplexType::class:
                return 'ComplexType';
        }

        return '';
    }

    /**
     * @param array $types
     * @return array
     * @throws \JDWil\Xsd\Exception\FileSystemException
     */
    protected function normalizeTypes(array $types): array
    {
        $ret = [];
        foreach ($types as $name) {
            if ($name instanceof Enum) {
                $ret[] = $name;
                continue;
            }
            $type = $classNs = $ns = '';
            $this->analyzeType($name, $type, $classNs, $ns);
            $ret[$type] = [
                'as' => str_replace(':', '', $name),
                'classNs' => $classNs
            ];
        }

        return $ret;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $classNamespace
     * @param string $namespace
     * @throws \JDWil\Xsd\Exception\FileSystemException
     */
    protected function analyzeType(string $name, string &$type, string &$classNamespace, string &$namespace)
    {
        $nsAlias = null;
        if (strpos($name, ':') !== false) {
            list($nsAlias, $name) = explode(':', $name);
        }

        if (is_string($nsAlias)) {
            $namespace = $this->definition->getNamespaceFromAlias($nsAlias);
            $element = $this->definition->findElementByName($name, $namespace);
        } else {
            $element = $this->definition->findElementByName($name);
            $namespace = $element->getSchema()->getXmlns();
        }

        if (null !== $element) {
            $classNamespace = $element->getSchema()->getClassNamespace();
        }

        if ($namespace === self::XSD_NAMESPACE) {
            $classified = Inflector::classify($name);
            if ($this->createXsdType($classified)) {
                $classNamespace = 'Xsd';
                $name = $classified;
            } else {
                $name = TypeUtil::typeToPhpPrimitive($name);
            }
        }

        $type = $name;
    }

    /**
     * @param string $ref
     * @param AbstractElement $element
     * @return AbstractElement
     * @throws TypeNotFoundException
     */
    protected function resolveReference(string $ref, AbstractElement $element): AbstractElement
    {
        list($namespace, $name) = $this->definition->determineNamespace($ref, $element);
        $ret = $this->definition->findElementByName($name, $namespace);
        if (null === $ret) {
            throw new TypeNotFoundException($ref);
        }

        return $ret;
    }

    /**
     * @param string $classNs
     * @param string|null $className
     * @return string
     */
    protected function classNamespace(string $classNs, string $className = null): string
    {
        if ($className) {
            return sprintf('%s\\%s\\%s', $this->options->namespacePrefix, $classNs, $className);
        } else {
            return sprintf('%s\\%s', $this->options->namespacePrefix, $classNs);
        }
    }

    protected function usesValidationException()
    {
        $this->class->uses($this->validationExceptionNs());
    }

    protected function usesOutputStream()
    {
        $this->class->uses(NamespaceUtil::classNamespace($this->options, 'Stream', 'OutputStream'));
    }

    /**
     * @return string
     */
    protected function validationExceptionNs(): string
    {
        return NamespaceUtil::classNamespace($this->options, 'Exception', 'ValidationException');
    }

    protected function createWriteXML()
    {
        $this->usesOutputStream();

        $body = '';
        $valueProperty = null;
        $attributes = $specifiers = [];
        $subRoutines = [];
        $needsClosingTag = false;
        foreach ($this->class->getProperties() as $property) {
            if ($property->name === 'value') {
                $needsClosingTag = true;
                $valueProperty = $property;
                continue;
            }

            if ($property->isAttribute) {
                $hasAttributes = true;
                //$specifiers[] = sprintf('%s="%s"', $property->name, str_replace("'", "", TypeUtil::typeSpecifier($property->type)));
                //$attributes[] = sprintf('$this->%s->getValue()', $property->name);
                $attributes[] = $property;
            } else {
                $needsClosingTag = true;
                $name = $property->name;
                if ($property->isCollection) {
                    $name = Inflector::singularize($name);
                }
                $subRoutine = <<<_SUB_
        if (null !== \$this->{$property->name}) {
            \$this->{$property->name}->writeXML(\$stream, '{$name}');
        }
_SUB_;

                $subRoutines[] = $subRoutine;
            }
        }

        $end = $needsClosingTag ? '>' : '/>';
        if (count($attributes)) {
            $body .= sprintf("        \$stream->write(sprintf('<%%s', \$tagName));\n");
            /** @var Property $attribute */
            foreach ($attributes as $attribute) {
                $getter = $attribute->isPrimitive() ? '' : '->getValue()';
                $accessor = $attribute->type === 'bool' ?
                    sprintf('var_export($this->%s%s, true)', $attribute->name, $getter) :
                    sprintf('$this->%s%s', $attribute->name, $getter);
                if (!$attribute->required && null === $attribute->default) {
                    $body .= sprintf("        if (null !== \$this->%s) {\n", $attribute->name);
                    $body .= sprintf("            \$stream->write(sprintf(' %s=\"%%s\" ', %s));\n", $attribute->name, $accessor);
                    $body .= sprintf("        }\n");
                } else {
                    $body .= sprintf("        \$stream->write(sprintf(' %s=\"%%s\" ', %s));\n", $attribute->name, $accessor);
                }
            }
            $body .= sprintf("        \$stream->write('%s');\n", $end);
        } else {
            $body .= sprintf("        \$stream->write(sprintf('<%%s%s', \$tagName));\n", $end);
        }

        if ($valueProperty) {
            $body .= "        \$stream->write(sprintf('%s', \$this->value));\n";
        }

        if (!empty($subRoutines)) {
            $body .= implode("\n", $subRoutines) . "\n";
        }

        if ($needsClosingTag) {
            $body .= "        \$stream->write(sprintf('</%s>', \$tagName));";
        }

        $method = new Method();
        $method->name = 'writeXML';
        $method->addArgument(new Argument('stream', 'OutputStream'));
        $method->addArgument(new Argument('tagName', 'string'));
        $method->body = $body;
        $this->class->addMethod($method);

    }
}
