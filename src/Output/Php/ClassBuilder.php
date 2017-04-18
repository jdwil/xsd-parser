<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use JDWil\Xsd\Exception\ValidationException;
use JDWil\Xsd\Stream\OutputStream;

/**
 * Class ClassBuilder
 * @package JDWil\Xsd\Output\Php
 */
class ClassBuilder
{
    const FINAL = 'final';
    const ABSTRACT = 'abstract';

    const TYPE_CLASS = 'class';
    const TYPE_INTERFACE = 'interface';
    const TYPE_TRAIT = 'trait';

    /**
     * @var array
     */
    private $declarations;

    /**
     * @var array
     */
    private $uses;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $docBlock;

    /**
     * @var string
     */
    private $classComment;

    /**
     * @var array
     */
    private $classModifiers;

    /**
     * @var string
     */
    private $classType;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $classExtends;

    /**
     * @var array
     */
    private $classImplements;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var array
     */
    private $methods;

    /**
     * ClassBuilder constructor.
     */
    public function __construct()
    {
        $this->declarations = [];
        $this->uses = [];
        $this->namespace = '';
        $this->docBlock = '';
        $this->classComment = '';
        $this->classModifiers = [];
        $this->classType = self::TYPE_CLASS,
        $this->className = '';
        $this->classExtends = '';
        $this->classImplements = [];
        $this->properties = [];
        $this->methods = [];
    }

    /**
     * @param string $declaration
     * @return ClassBuilder
     */
    public function addDeclaration(string $declaration): ClassBuilder
    {
        $this->declarations[] = $declaration;
        return $this;
    }

    /**
     * @param string $use
     * @return ClassBuilder
     */
    public function uses(string $use): ClassBuilder
    {
        $this->uses[] = $use;
        return $this;
    }

    /**
     * @param string $namespace
     * @return ClassBuilder
     */
    public function setNamespace(string $namespace): ClassBuilder
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param string $modifier
     * @return ClassBuilder
     * @throws ValidationException
     */
    public function addModifier(string $modifier): ClassBuilder
    {
        if ($modifier !== self::FINAL && $modifier !== self::ABSTRACT) {
            throw new ValidationException('modifier can only be FINAL or ABSTRACT');
        }
        $this->classModifiers[] = $modifier;
        return $this;
    }

    /**
     * @param string $docBlock
     * @return ClassBuilder
     */
    public function setDocBlock(string $docBlock): ClassBuilder
    {
        $this->docBlock = $docBlock;
        return $this;
    }

    /**
     * @param string $classComment
     * @return ClassBuilder
     */
    public function setClassComment(string $classComment): ClassBuilder
    {
        $this->classComment = $classComment;
        return $this;
    }

    /**
     * @param string $classType
     * @return ClassBuilder
     * @throws ValidationException
     */
    public function setClassType(string $classType): ClassBuilder
    {
        if (!in_array($classType, [self::TYPE_CLASS, self::TYPE_INTERFACE, self::TYPE_TRAIT])) {
            throw new ValidationException('Class type must be class, interface or trait');
        }
        $this->classType = $classType;
        return $this;
    }

    /**
     * @param string $className
     * @return ClassBuilder
     */
    public function setClassName(string $className): ClassBuilder
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @param string $classExtends
     * @return ClassBuilder
     */
    public function setClassExtends(string $classExtends): ClassBuilder
    {
        $this->classExtends = $classExtends;
        return $this;
    }

    /**
     * @param string $implements
     * @return ClassBuilder
     */
    public function addImplements(string $implements): ClassBuilder
    {
        $this->classImplements[] = $implements;
        return $this;
    }

    /**
     * @param \stdClass $property
     * @return ClassBuilder
     */
    public function addProperty(\stdClass $property): ClassBuilder
    {
        $this->properties[] = $property;
        return $this;
    }

    /**
     * @return PropertyBuilder
     */
    public function property(): PropertyBuilder
    {
        return new PropertyBuilder();
    }

    public function writeTo(OutputStream $stream)
    {
        $stream->writeLine('<?php');
        if (count($this->declarations)) {
            $this->writeLines($this->declarations, $stream);
        }

        if (!empty($this->docBlock)) {
            $stream->writeLine($this->docBlock);
        }

        if (!empty($this->namespace)) {
            $stream->writeLine($this->namespace);
        }

        if (count($this->uses)) {
            $this->writeLines($this->uses, $stream);
        }

        if (!empty($this->classComment)) {
            $stream->writeLine($this->classComment);
        }

        if (!empty($this->classModifiers)) {
            foreach ($this->classModifiers as $modifier) {
                $stream->write(sprintf("%s ", $modifier));
            }
        }
        $stream->write(sprintf("%s ", $this->classType));

        if (!empty($this->classExtends)) {
            $stream->write(sprintf("extends %s ", $this->classExtends));
        }

        if (count($this->classImplements)) {
            foreach ($this->classImplements as $implements) {

            }
        }
    }

    /**
     * @param array $lines
     * @param OutputStream $stream
     */
    private function writeLines(array $lines, OutputStream $stream)
    {
        foreach ($lines as $line) {
            $stream->writeLine($line);
        }
    }
}