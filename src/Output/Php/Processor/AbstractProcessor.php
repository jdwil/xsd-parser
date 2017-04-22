<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use Doctrine\Common\Inflector\Inflector;
use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\AbstractElement;
use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Element\SimpleType;
use JDWil\Xsd\Exception\FileSystemException;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\Argument;
use JDWil\Xsd\Output\Php\ClassBuilder;
use JDWil\Xsd\Output\Php\Method;
use JDWil\Xsd\Output\Php\Property;
use JDWil\Xsd\Stream\OutputStream;
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
     * AbstractProcessor constructor.
     * @param Options $options
     * @param Definition $definition
     */
    public function __construct(Options $options, Definition $definition)
    {
        $this->options = $options;
        $this->definition = $definition;
        $this->class = new ClassBuilder($options);
    }

    protected function initializeClass()
    {
        if ($this->options->declareStrictTypes) {
            $this->class->addDeclaration('declare(strict_types=1);');
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
            $type = array_pop(explode(':', $type));
        }
        $className = sprintf('%sCollection', $type);
        $builder = new ClassBuilder($this->options);
        $builder
            ->setNamespace(sprintf('%s\\ValueObject', $this->options->namespacePrefix))
            ->setClassName($className)
            ->uses(sprintf('use %s\\Exception\\ValidationException;', $this->options->namespacePrefix))
            ->uses(sprintf('use %s\\Stream\\OutputStream;', $this->options->namespacePrefix))
            ->uses(sprintf('use %s;', $namespace))
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
     */
    protected function normalizeTypes(array $types): array
    {
        $type = $classNs = $ns = '';
        $ret = [];
        foreach ($types as $name) {
            if ($name instanceof Enum) {
                $ret[] = $name;
                continue;
            }
            $this->analyzeType($name, $type, $classNs, $ns);
            $ret[$type] = $classNs;
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

        $classNamespace = $this->getTypeNamespace($name, $namespace);
        if ($namespace === self::XSD_NAMESPACE) {
            $name = Inflector::classify($name);
            if ($this->createXsdType($name)) {
                $classNamespace = 'Xsd';
            }
        }

        $type = $name;
    }

    /**
     * @param string $ref
     * @param AbstractElement $element
     * @return AbstractElement
     * @throws \Exception
     */
    protected function resolveReference(string $ref, AbstractElement $element): AbstractElement
    {
        list($namespace, $name) = $this->definition->determineNamespace($ref, $element);
        $ret = $this->definition->findElementByName($name, $namespace);
        if (null === $ret) {
            throw new \Exception(sprintf('Type not found: %s', $ref));
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
        $this->class->uses(sprintf('use %s;', $this->validationExceptionNs()));
    }

    protected function usesOutputStream()
    {
        $this->class->uses(sprintf('use %s\\Stream\\OutputStream;', $this->options->namespacePrefix));
    }

    /**
     * @return string
     */
    protected function validationExceptionNs(): string
    {
        return sprintf('%s\\Exception\\ValidationException', $this->options->namespacePrefix);
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
            //%s));\n", implode(' ', $specifiers), $end, implode(', ', $attributes));
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
                    $body .= sprintf("            \$stream->write(sprintf(' %s=\"%%s\" ', %s));\n", $attribute->name, $accessor);
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
