<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use JDWil\Xsd\Element\SimpleType;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\ClassBuilder;

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
     * @var ClassBuilder
     */
    protected $class;

    /**
     * @var Options
     */
    protected $options;

    /**
     * SimpleTypeProcessor constructor.
     * @param SimpleType $type
     * @param Options $options
     */
    public function  __construct(SimpleType $type, Options $options)
    {
        $this->type = $type;
        $this->options = $options;
        $this->class = new ClassBuilder($options);
    }

    /**
     * @return ClassBuilder
     */
    public function buildClass(): ClassBuilder
    {
        $this->class->setClassName($this->type->getName());
        $this->initializeClass();
        return $this->class;
    }

    protected function processAttributes()
    {
        foreach ($this->type->getChildren() as $child) {

        }
    }
}