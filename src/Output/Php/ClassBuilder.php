<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use JDWil\Xsd\Exception\ValidationException;
use JDWil\Xsd\Options;
use JDWil\Xsd\Stream\OutputStream;
use JDWil\Xsd\Util\TypeUtil;

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
     * @var Options
     */
    private $options;

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
    public function __construct(Options $options)
    {
        $this->options = $options;
        $this->declarations = [];
        $this->uses = [];
        $this->namespace = '';
        $this->docBlock = '';
        $this->classComment = '';
        $this->classModifiers = [];
        $this->classType = self::TYPE_CLASS;
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
        if (!in_array($classType, [self::TYPE_CLASS, self::TYPE_INTERFACE, self::TYPE_TRAIT], true)) {
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

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param OutputStream $stream
     */
    public function writeTo(OutputStream $stream)
    {
        $stream->writeLine('<?php');
        if (count($this->declarations)) {
            $this->writeLines($this->declarations, $stream);
        }

        if (!empty($this->docBlock)) {
            $stream->writeLine($this->docBlock);
            $stream->write("\n");
        }

        if (!empty($this->namespace)) {
            $stream->writeLine(sprintf('namespace %s;', $this->namespace));
            $stream->write("\n");
        }

        if (count($this->uses)) {
            $this->writeLines($this->uses, $stream);
        }

        if (!empty($this->classComment)) {
            $stream->writeLine($this->classComment);
        }

        if (!empty($this->classModifiers)) {
            foreach ($this->classModifiers as $modifier) {
                $stream->write(sprintf('%s ', $modifier));
            }
        }
        $stream->write(sprintf('%s %s ', $this->classType, $this->className));

        if (!empty($this->classExtends)) {
            $stream->write(sprintf('extends %s ', $this->classExtends));
        }

        if (count($this->classImplements)) {
            $stream->write(sprintf('%s', $this->classImplements[0]));
            for ($iMax = count($this->classImplements), $i = 1; $i < $iMax; $i++) {
                $stream->write(sprintf('%s, ', $this->classImplements[$i]));
            }
        }

        $stream->write("\n");
        $stream->writeLine('{');

        if (!empty($this->properties)) {
            foreach ($this->properties as $key => $property) {
                $stream->writeLine('    /**');
                $stream->writeLine(sprintf('     * @var %s', $property->type));
                $stream->writeLine('     */');
                $stream->writeLine(sprintf('    private $%s;', $property->name));
                $stream->write("\n");
            }

            $stream->writeLine('    /**');
            $stream->writeLine(sprintf('     * %s constructor', $this->className));
            $stream->writeLine('     */');
            $stream->write('    public function __construct(');

            $i = 0;
            while (isset($this->properties[$i]) && $this->properties[$i]->fixed) {
                $i++;
            }

            if (isset($this->properties[$i])) {
                $this->writeMethodArgument($this->properties[$i], $stream);
                for ($iMax = count($this->properties), ++$i; $i < $iMax; $i++) {
                    if (!$this->properties[$i]->fixed) {
                        $stream->write(', ');
                        $this->writeMethodArgument($this->properties[$i], $stream);
                    }
                }
            }
            $stream->writeLine(')');
            $stream->writeLine('    {');
            foreach ($this->properties as $property) {
                if ($property->fixed) {
                    $value = is_string($property->default) ? sprintf("'%s'", $property->default) : $property->default;
                    $stream->writeLine(sprintf('        $this->%s = %s;', $property->name, $value));
                } else if ($this->isNonPrimitiveWithDefault($property)) {
                    $stream->writeLine(sprintf('        $this->%s = new %s($%s);',
                        $property->name,
                        $property->type,
                        $property->name
                    ));
                } else {
                    $stream->writeLine(sprintf('        $this->%s = $%s;', $property->name, $property->name));
                }
            }
            $stream->writeLine('    }');
            $stream->write("\n");

            foreach ($this->properties as $index => $property) {
                if ($property->fixed) {
                    continue;
                }

                $stream->writeLine('    /**');
                $stream->writeLine(sprintf('     * @return %s', $property->type));
                $stream->writeLine('     */');
                if ($property->required) {
                    $stream->writeLine(sprintf('    public function get%s(): %s', ucwords($property->name), $property->type));
                } else {
                    if ($this->options->phpVersion === '7.0') {
                        $stream->writeLine(sprintf('    public function get%s()', ucwords($property->name)));
                    } else {
                        $stream->writeLine(sprintf('    public function get%s():? %s', ucwords($property->name), $property->type));
                    }
                }
                $stream->writeLine('    {');
                $stream->writeLine(sprintf('        return $this->%s;', $property->name));
                $stream->writeLine('    }');
                $stream->write("\n");

                $stream->writeLine('    /**');
                $stream->writeLine(sprintf('     * @param %s $%s', $property->type, $property->name));
                $stream->writeLine('     */');
                $stream->writeLine(sprintf('    public function set%s(%s $%s)',
                    ucwords($property->name),
                    $property->type,
                    $property->name
                ));
                $stream->writeLine('    {');
                $stream->writeLine(sprintf('        $this->%s = $%s;', $property->name, $property->name));
                $stream->writeLine('    }');

                if (isset($this->properties[$index + 1])) {
                    $stream->write("\n");
                }
            }
        }

        $stream->writeLine('}');
    }

    /**
     * @param \stdClass $property
     * @return bool
     */
    private function isNonPrimitiveWithDefault(\stdClass $property)
    {
        return !TypeUtil::isPrimitive($property->type) && $property->default;
    }

    /**
     * @param \stdClass $property
     * @param OutputStream $stream
     */
    private function writeMethodArgument(\stdClass $property, OutputStream $stream)
    {
        $type = $property->type;
        if (!TypeUtil::isPrimitive($type)) {
            $type = TypeUtil::getVarType($property->default);
        }
        $stream->write(sprintf('%s $%s', $type, $property->name));
        if ($property->default) {
            $default = is_string($property->default) ? sprintf("'%s'", $property->default) : $property->default;
            $stream->write(sprintf(' = %s', $default));
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
        $stream->write("\n");
    }
}
