<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

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

abstract class AbstractProcessor implements ProcessorInterface
{
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
            $this->class->addDeclaration('declare(string_types=1);');
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

        $dir = sprintf('%s/ValueObject', $this->options->outputDirectory);
        if (!@mkdir($dir) && !is_dir($dir)) {
            throw new FileSystemException(sprintf('Could not create directory %s', $dir));
        }
        $builder->writeTo(OutputStream::streamedTo(sprintf('%s/%s.php', $dir, $className)));

        return $builder;
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
        $ret = [];
        foreach ($types as $type) {
            list($ns, $name) = $this->normalizeType($type);
            if (null !== $ns) {
                $ret[$ns] = $name;
            } else {
                $ret[] = $name;
            }
        }

        return $ret;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $classNamespace
     * @param string $namespace
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
        $type = $name;
    }
}
