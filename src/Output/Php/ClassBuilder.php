<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use Doctrine\Common\Inflector\Inflector;
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
    const DEFAULT_COLLECTION_NAME = 'items';

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
     * @var Property[]
     */
    private $properties;

    /**
     * @var array
     */
    private $constants;

    /**
     * @var Method[]
     */
    private $methods;

    /**
     * @var bool
     */
    private $simpleType;

    /**
     * @var int
     */
    private $minValue;

    /**
     * @var int
     */
    private $maxValue;

    /**
     * @var int
     */
    private $totalDigits;

    /**
     * @var int
     */
    private $fractionDigits;

    /**
     * @var int
     */
    private $valueLength;

    /**
     * @var int
     */
    private $valueMinLength;

    /**
     * @var int
     */
    private $valueMaxLength;

    /**
     * @var array
     */
    private $enumerations;

    /**
     * @var string
     */
    private $whiteSpace;

    /**
     * @var string
     */
    private $valuePattern;

    /**
     * @var array
     */
    private $validators;

    /**
     * ClassBuilder constructor.
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
        $this->declarations = [];
        $this->uses = [];
        $this->constants = [];
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
        $this->validators = [];
    }

    /**
     * @param string $declaration
     * @return ClassBuilder
     */
    public function addDeclaration(string $declaration): ClassBuilder
    {
        if (!in_array($declaration, $this->declarations, true)) {
            $this->declarations[] = $declaration;
        }
        return $this;
    }

    /**
     * @param string $validator
     * @return ClassBuilder
     */
    public function addValidator(string $validator): ClassBuilder
    {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * @param string $use
     * @return ClassBuilder
     */
    public function uses(string $use): ClassBuilder
    {
        if (!in_array($use, $this->uses, true)) {
            $this->uses[] = $use;
        }
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
     * @param Method $method
     * @return ClassBuilder
     */
    public function addMethod(Method $method): ClassBuilder
    {
        $this->methods[] = $method;
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
     * @param Property $property
     * @return ClassBuilder
     */
    public function addProperty(Property $property): ClassBuilder
    {
        $this->properties[] = $property;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param bool $simpleType
     */
    public function setSimpleType(bool $simpleType)
    {
        $this->simpleType = $simpleType;
    }

    /**
     * @param int $minValue
     */
    public function setMinValue(int $minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * @param int $maxValue
     */
    public function setMaxValue(int $maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * @param int $totalDigits
     */
    public function setTotalDigits(int $totalDigits)
    {
        $this->totalDigits = $totalDigits;
    }

    /**
     * @param int $fractionDigits
     */
    public function setFractionDigits(int $fractionDigits)
    {
        $this->fractionDigits = $fractionDigits;
    }

    /**
     * @param int $valueLength
     */
    public function setValueLength(int $valueLength)
    {
        $this->valueLength = $valueLength;
    }

    /**
     * @param int $valueMinLength
     */
    public function setValueMinLength(int $valueMinLength)
    {
        $this->valueMinLength = $valueMinLength;
    }

    /**
     * @param int $valueMaxLength
     */
    public function setValueMaxLength(int $valueMaxLength)
    {
        $this->valueMaxLength = $valueMaxLength;
    }

    /**
     * @param array $enumerations
     */
    public function setEnumerations(array $enumerations)
    {
        $this->enumerations = $enumerations;
    }

    /**
     * @param $enumeration
     */
    public function addEnumeration($enumeration)
    {
        $this->enumerations[] = $enumeration;
    }

    /**
     * @param string $whiteSpace
     */
    public function setWhiteSpace(string $whiteSpace)
    {
        $this->whiteSpace = $whiteSpace;
    }

    /**
     * @param string $valuePattern
     */
    public function setValuePattern(string $valuePattern)
    {
        $this->valuePattern = $valuePattern;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function addConstant(string $name, $value)
    {
        $this->constants[$name] = $value;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return string[]
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param OutputStream $stream
     */
    public function writeTo(OutputStream $stream)
    {
        $this->sortProperties();

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
        $stream->write(sprintf('%s %s', $this->classType, $this->className));

        if (!empty($this->classExtends)) {
            $stream->write(sprintf(' extends %s', $this->classExtends));
        }

        if (count($this->classImplements)) {
            $stream->write(sprintf(' %s', $this->classImplements[0]));
            for ($iMax = count($this->classImplements), $i = 1; $i < $iMax; $i++) {
                $stream->write(sprintf(' %s,', $this->classImplements[$i]));
            }
        }

        $stream->write("\n");
        $stream->writeLine('{');

        if (!empty($this->constants)) {
            foreach ($this->constants as $name => $value) {
                $specifier = TypeUtil::typeSpecifier($value);
                if ($this->options->phpVersion === '7.0') {
                    $stream->writeLine(sprintf("    const %s = {$specifier};", $name, $value));
                } else {
                    $stream->writeLine(sprintf("    public const %s = {$specifier};", $name, $value));
                }
            }
            $stream->write("\n");
        }

        if (!empty($this->properties)) {
            $this->writeProperties($stream);
            $this->writeConstructorDocBlock($stream);
            $this->writeConstructor($stream);
            $this->writeGettersAndSetters($stream);
        }
        $this->writeOtherMethods($stream);

        $stream->writeLine('}');
    }

    /**
     * @param OutputStream $stream
     */
    private function writeProperties(OutputStream $stream)
    {
        foreach ($this->properties as $key => $property) {
            $stream->writeLine('    /**');
            $stream->writeLine(sprintf('     * @var %s', $property->type));
            $stream->writeLine('     */');
            $stream->writeLine(sprintf('    %s $%s;', $property->visibility, $property->name));
            $stream->write("\n");
        }
    }

    /**
     * @param OutputStream $stream
     */
    private function writeConstructorDocBlock(OutputStream $stream)
    {
        $stream->writeLine('    /**');
        $stream->writeLine(sprintf('     * %s constructor', $this->className));
        foreach ($this->properties as $property) {
            if ($this->includeInConstructor($property)) {
                $type = $property->type;
                if ($type && null !== $property->default && !TypeUtil::isPrimitive($type)) {
                    $type = TypeUtil::getVarType($property->default);
                }
                if ($type) {
                    $stream->writeLine(sprintf('     * @param %s $%s', $type, $property->name));
                } else {
                    $stream->writeLine(sprintf('     * @param mixed $%s', $property->name));
                }
            }
        }
        if ($this->hasValidators()) {
            $stream->writeLine('     * @throws ValidationException');
        }
        $stream->writeLine('     */');
    }

    /**
     * @param OutputStream $stream
     */
    private function writeConstructor(OutputStream $stream)
    {
        /**
         * Write the constructor
         */
        $stream->write('    public function __construct(');

        /**
         * Set class properties within the constructor.
         */
        $i = 0;
        while (isset($this->properties[$i]) && !$this->includeInConstructor($this->properties[$i])) {
            $i++;
        }

        if (isset($this->properties[$i])) {
            $this->writeMethodArgument($this->properties[$i], $stream);
            for ($iMax = count($this->properties), ++$i; $i < $iMax; $i++) {
                if ($this->includeInConstructor($this->properties[$i])) {
                    $stream->write(', ');
                    $this->writeMethodArgument($this->properties[$i], $stream);
                }
            }
        }

        $stream->writeLine(')');
        $stream->writeLine('    {');

        /**
         * Set properties within constructor
         */
        foreach ($this->properties as $property) {
            if ($property->fixed) {
                if (null !== $property->default && TypeUtil::isPrimitive($property->default)) {
                    $specifier = TypeUtil::typeSpecifier($property->default);
                    $value = $property->default;
                    if (is_array($value)) {
                        $value = '[]';
                    }
                    $stream->writeLine(sprintf("        \$this->%s = {$specifier};", $property->name, $value));
                } else {
                    $stream->writeLine(sprintf('        $this->%s = new %s();', $property->name, $property->default));
                }
            } else if ($this->isNonPrimitiveWithDefault($property)) {
                $stream->writeLine(sprintf('        $this->%s = new %s($%s);',
                    $property->name,
                    $property->type,
                    $property->name
                ));
            } else if ($this->includeInConstructor($property)) {
                $stream->writeLine(sprintf('        $this->%s = $%s;', $property->name, $property->name));
            }
        }

        /**
         * Write parameter validations
         */
        $this->writeConstructorValidators($stream);

        /**
         * Custom validators
         */
        foreach ($this->validators as $validator) {
            $stream->writeLine($validator);
        }

        /**
         * End of constructor
         */
        $stream->writeLine('    }');
    }

    /**
     * @param OutputStream $stream
     */
    private function writeGettersAndSetters(OutputStream $stream)
    {
        /**
         * Getters and Setters
         */
        foreach ($this->properties as $index => $property) {
            if ($property->fixed) {
                continue;
            }

            if ($this->needsGetter($property)) {
                $this->writeGetter($property, $stream);
            }

            if ($this->needsAller($property)) {
                $this->writeAller($property, $stream);
            }

            if ($this->needsSetter($property)) {
                $this->writeSetter($property, $stream);
            }

            if ($this->needsAdder($property)) {
                $this->writeAdder($property, $stream);
            }
        }
    }

    /**
     * @param Property $property
     * @param OutputStream $stream
     */
    private function writeGetter(Property $property, OutputStream $stream)
    {
        $stream->write("\n");
        $stream->writeLine('    /**');
        $stream->writeLine(sprintf('     * @return %s', $property->type ?? 'mixed'));
        $stream->writeLine('     */');
        if ($property->required && $property->type) {
            $stream->writeLine(sprintf('    public function get%s(): %s', ucwords($property->name), $property->type));
        } else {
            if ($this->options->phpVersion === '7.0' || !$property->type) {
                $stream->writeLine(sprintf('    public function get%s()', ucwords($property->name)));
            } else {
                $stream->writeLine(sprintf('    public function get%s():? %s', ucwords($property->name), $property->type));
            }
        }
        $stream->writeLine('    {');
        $stream->writeLine(sprintf('        return $this->%s;', $property->name));
        $stream->writeLine('    }');
    }

    /**
     * @param Property $property
     * @param OutputStream $stream
     */
    private function writeSetter(Property $property, OutputStream $stream)
    {
        $stream->write("\n");
        $stream->writeLine('    /**');
        $stream->writeLine(sprintf('     * @param %s $%s', $property->type, $property->name));
        if ($property->choiceGroup) {
            $stream->writeLine('     * @throws ValidationException');
        }
        $stream->writeLine('     */');
        $stream->writeLine(sprintf('    public function set%s(%s $%s)',
            ucwords($property->name),
            $property->type,
            $property->name
        ));
        $stream->writeLine('    {');
        if ($property->choiceGroup) {
            $statements = [];
            $names = [];
            foreach ($this->getPropertiesInGroup($property) as $otherProperty) {
                $names[] = sprintf('$%s', $otherProperty->name);
                if ($property->name === $otherProperty->name) {
                    continue;
                }
                $statements[] = sprintf('null !== $this->%s', $otherProperty->name);
            }
            $if = implode(' || ', $statements);
            $stream->writeLine(sprintf('        if (%s) {', $if));
            $stream->writeLine(sprintf(
                '            throw new ValidationException(\'only one of %s allowed in group\');',
                implode(', ', $names)
            ));
            $stream->writeLine('        }');
        }
        $stream->writeLine(sprintf('        $this->%s = $%s;', $property->name, $property->name));
        $stream->writeLine('    }');
    }

    /**
     * @param Property $property
     * @param OutputStream $stream
     */
    private function writeAller(Property $property, OutputStream $stream)
    {
        $stream->write("\n");
        $methodName = 'all';
        if ($property->name !== self::DEFAULT_COLLECTION_NAME) {
            $methodName = sprintf('all%s', Inflector::pluralize(Inflector::classify($property->name)));
        }

        $stream->writeLine('    /**');
        $stream->writeLine('     * @returns array');
        if ($property->collectionMin !== 0) {
            $stream->writeLine('     * @throws ValidationException');
        }
        $stream->writeLine('     */');
        $stream->writeLine(sprintf('    public function %s(): array', $methodName));
        $stream->writeLine('    {');
        $target = $methodName === 'all' ? 'items' : sprintf('%s->all()', $property->name);
        if ($property->collectionMin !== 0) {
            $stream->writeLine(sprintf('        $ret = $this->%s;', $target));
            $stream->writeLine(sprintf('        if (%d < $ret) {', $property->collectionMin));
            $stream->writeLine(sprintf(
                '            throw new ValidationException(\'collection must have at least %d members\');',
                $property->collectionMin
            ));
            $stream->writeLine('        }');
            $stream->write("\n");
            $stream->writeLine('        return $ret;');
        } else {
            $stream->writeLine(sprintf('        return $this->%s;', $target));
        }
        $stream->writeLine('    }');
    }

    /**
     * @param Property $property
     * @param OutputStream $stream
     */
    private function writeAdder(Property $property, OutputStream $stream)
    {
        $stream->write("\n");
        $methodName = 'add';
        if ($property->name !== self::DEFAULT_COLLECTION_NAME) {
            $methodName = sprintf('add%s', Inflector::classify(Inflector::singularize($property->name)));
        }

        $variable = Inflector::singularize($property->name);
        $stream->writeLine('    /**');
        $stream->writeLine(sprintf('     * @param %s $%s', $property->collectionOf, $variable));
        $stream->writeLine('     */');
        $stream->writeLine(sprintf('    public function %s(%s $%s)', $methodName, $property->collectionOf, $variable));
        $stream->writeLine('    {');
        if ($methodName === 'add') {
            $stream->writeLine(sprintf('        $this->items->add($%s);', $variable));
        } else {
            $stream->writeLine(sprintf('        $this->%s->add($%s);', $property->name, $variable));
        }
        $stream->writeLine('    }');
    }

    /**
     * @param OutputStream $stream
     */
    private function writeOtherMethods(OutputStream $stream)
    {
        /**
         * Other methods
         */
        foreach ($this->methods as $method) {
            if (count($method->arguments) || $method->returns) {
                $stream->write("\n");
                $stream->writeLine('    /**');
                foreach ($method->arguments as $argument) {
                    $stream->writeLine(sprintf('     * @param %s $%s', $argument->type, $argument->name));
                }

                if ($method->returns) {
                    if ($method->returnsNull) {
                        $stream->writeLine(sprintf('     * @returns null|%s', $method->returns));
                    } else {
                        $stream->writeLine(sprintf('     * @returns %s', $method->returns));
                    }
                }

                if (count($method->throws)) {
                    foreach ($method->throws as $throws) {
                        $stream->writeLine(sprintf('     * @throws %s', $throws));
                    }
                }
                $stream->writeLine('     */');
            }

            $stream->write(sprintf('    %s function %s(', $method->visibility, $method->name));
            if (count($method->arguments)) {
                $argument = $method->arguments[0];
                $stream->write(sprintf('%s $%s', $argument->type, $argument->name));
                if (null !== $argument->default) {
                    $specification = TypeUtil::typeSpecifier($argument->default);
                    $stream->write(sprintf(" = {$specification}", $argument->default));
                }

                for ($iMax = count($method->arguments), $i = 1; $i < $iMax; $i++) {
                    $stream->write(sprintf(', %s $%s', $method->arguments[$i]->type, $method->arguments[$i]->name));
                    if (null !== $method->arguments[$i]->default) {
                        $specification = TypeUtil::typeSpecifier($method->arguments[$i]->default);
                        $stream->write(sprintf(" = {$specification}", $method->arguments[$i]->default));
                    }
                }
            }
            $stream->write(')');

            if (false !== $method->returns) {
                if ($method->returnsNull) {
                    if ($this->options->phpVersion === '7.1') {
                        $stream->writeLine(sprintf(':? %s', $method->returns));
                    } else {
                        $stream->write("\n");
                    }
                } else {
                    $stream->writeLine(sprintf(': %s', $method->returns));
                }
            } else {
                $stream->write("\n");
            }

            $stream->writeLine('    {');
            $stream->writeLine($method->body);
            $stream->writeLine('    }');
        }
    }

    /**
     * @param Property $property
     * @return bool
     */
    private function needsSetter(Property $property): bool
    {
        return (!$property->immutable && !$property->isCollection);
    }

    /**
     * @param Property $property
     * @return bool
     */
    private function needsGetter(Property $property): bool
    {
        return !$property->isCollection && $property->createGetter;
    }

    /**
     * @param Property $property
     * @return bool
     */
    private function needsAdder(Property $property): bool
    {
        return !$property->immutable && $property->isCollection;
    }

    /**
     * @param Property $property
     * @return bool
     */
    private function needsAller(Property $property): bool
    {
        return $property->isCollection;
    }

    /**
     * @param Property $property
     * @return Property[]
     */
    private function getPropertiesInGroup(Property $property): array
    {
        $ret = [];
        $groupId = $property->choiceGroup;
        foreach ($this->properties as $otherProperty) {
            if ($otherProperty->choiceGroup === $groupId) {
                $ret[] = $otherProperty;
            }
        }

        return $ret;
    }

    /**
     * @param Property $property
     * @return bool
     */
    private function includeInConstructor(Property $property): bool
    {
        if (!$property->includeInConstructor) {
            return false;
        }

        if ($property->fixed) {
            return false;
        }

        if (null !== $property->choiceGroup) {
            return false;
        }

        if (!$property->required && !$property->default) {
            return false;
        }

        return true;
    }

    /**
     * @param Property $property
     * @return bool
     */
    private function isNonPrimitiveWithDefault(Property $property)
    {
        return $property->type && !TypeUtil::isPrimitive($property->type) && $property->default;
    }

    private function sortProperties()
    {
        usort($this->properties, function ($p1, $p2) {
            if (null !== $p1->default && null === $p2->default) {
                return 1;
            } else {
                return -1;
            }
        });
    }

    /**
     * @param Property $property
     * @param OutputStream $stream
     */
    private function writeMethodArgument(Property $property, OutputStream $stream)
    {
        $type = $property->type;
        if ($type && null !== $property->default && !TypeUtil::isPrimitive($type)) {
            $type = TypeUtil::getVarType($property->default);
            if ($property->name === 'dpi') {
                echo $property->default . "\n";
            }
        }
        if ($type) {
            $stream->write(sprintf('%s $%s', $type, $property->name));
        } else {
            $stream->write(sprintf('$%s', $property->name));
        }
        if ($property->default) {
            if (TypeUtil::isPrimitive($type)) {
                $specifier = TypeUtil::typeSpecifier($type);
            } else {
                $specifier = TypeUtil::typeSpecifier($property->default);
            }
            $stream->write(sprintf(" = {$specifier}", $property->default));
        }
    }

    /**
     * @return bool
     */
    private function hasValidators(): bool
    {
        return
            null !== $this->minValue ||
            null !== $this->maxValue ||
            null !== $this->totalDigits ||
            null !== $this->fractionDigits ||
            null !== $this->valueLength ||
            null !== $this->valueMinLength ||
            null !== $this->valueMaxLength ||
            null !== $this->valuePattern ||
            null !== $this->enumerations ||
            count($this->validators) > 0;
    }

    /**
     * @param OutputStream $stream
     */
    private function writeConstructorValidators(OutputStream $stream)
    {
        if (!$this->simpleType) {
            return;
        }

        if (null !== $this->minValue) {
            $stream->write("\n");
            $minSpecifier = TypeUtil::typeSpecifier($this->minValue);
            $stream->writeLine(sprintf("        if (\$this->value < {$minSpecifier}) {", $this->minValue));
            $stream->writeLine('            throw new ValidationException(\'value out of bounds\');');
            $stream->writeLine('        }');
        }

        if (null !== $this->maxValue) {
            $stream->write("\n");
            $maxSpecifier = TypeUtil::typeSpecifier($this->maxValue);
            $stream->writeLine(sprintf("        if (\$this->value > {$maxSpecifier}) {", $this->maxValue));
            $stream->writeLine('            throw new ValidationException(\'value out of bounds\');');
            $stream->writeLine('        }');
        }

        if (null !== $this->totalDigits) {
            $stream->write("\n");
            $stream->writeLine(sprintf('        if (%d !== preg_match_all(\'/[0-9]/\', $this->value)) {',
                $this->totalDigits
            ));
            $stream->writeLine(sprintf('            throw new ValidationException(\'value must contain %d digits\'',
                $this->totalDigits
            ));
            $stream->writeLine('        }');
        }

        if (null !== $this->fractionDigits) {
            $stream->write("\n");
            $stream->writeLine(
                '        $decimals = ((int) $this->value != $this->value) ' .
                '? (strlen($this->value) - strpos($this->value, \'.\')) - 1 : 0;'
            );
            $stream->writeLine(sprintf('        if (%d !== $decimals) {', $this->fractionDigits));
            $stream->writeLine(sprintf(
                '            throw new ValidationException(\'value can only contain %d decimal digits\');',
                $this->fractionDigits
            ));
            $stream->writeLine('        }');
        }

        if (null !== $this->valueLength) {
            $stream->write("\n");
            $stream->writeLine(sprintf('        if (%d !== strlen($this->value)) {', $this->valueLength));
            $stream->writeLine(sprintf('            throw new ValidationException(\'value must be %d characters\');',
                $this->valueLength
            ));
            $stream->writeLine('        }');
        }

        if (null !== $this->valueMinLength) {
            $stream->write("\n");
            $stream->writeLine(sprintf('        if (%d > strlen($this->value)) {', $this->valueMinLength));
            $stream->writeLine(sprintf(
                '            throw new ValidationException(\'value must be more than %d characters\');',
                $this->valueMinLength
            ));
            $stream->writeLine('        }');
        }

        if (null !== $this->valueMaxLength) {
            $stream->write("\n");
            $stream->writeLine(sprintf('        if (%d < strlen($this->value)) {', $this->valueMaxLength));
            $stream->writeLine(sprintf(
                '            throw new ValidationException(\'value must be less than %d characters\');',
                $this->valueMaxLength
            ));
            $stream->writeLine('        }');
        }

        if (null !== $this->valuePattern) {
            $stream->write("\n");
            $pattern = str_replace(['[0-9]', '/'], ['\\d', '\\/'], $this->valuePattern);
            $stream->writeLine(sprintf('        if (!preg_match(\'/%s/\', $this->value)) {', $pattern));
            $stream->writeLine(sprintf(
                '            throw new ValidationException(\'value does not match pattern "%s"\');',
                $pattern
            ));
            $stream->writeLine('        }');
        }

        if (null !== $this->enumerations) {
            $stream->write("\n");
            $constants = [];
            foreach ($this->enumerations as $enumeration) {
                $constants[] = sprintf('self::VALUE_%s', strtoupper($enumeration));
            }
            $string = implode(', ', $constants);
            $wrapped = false;
            if (strlen($string) >= 90) {
                $string = str_replace("\n", "\n            ", wordwrap($string, 90));
                $wrapped = true;
            }
            if ($wrapped) {
                $stream->writeLine('        if (!in_array($this->value, [');
                $stream->writeLine(sprintf('            %s', $string));
                $stream->writeLine('        ], true)) {');
                $stream->writeLine('            throw new ValidationException(\'');
                $string = str_replace("\n", "\n    ", $string);
                $stream->writeLine(sprintf('                value must be one of %s', $string));
                $stream->writeLine('            \');');
            } else {
                $stream->writeLine(sprintf('        if (!in_array($this->value, [%s], true)) {', $string));
                $stream->writeLine(sprintf('            throw new ValidationException(\'value must be one of %s\');',
                    $string
                ));
            }
            $stream->writeLine('        }');
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
