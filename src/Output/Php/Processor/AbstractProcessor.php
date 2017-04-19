<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use JDWil\Xsd\Element\AbstractElement;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\ClassBuilder;

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
     * @var AbstractElement
     */
    protected $type;

    protected function initializeClass()
    {

        $this->class->setNamespace($this->options->namespacePrefix);
        if ($this->options->declareStrictTypes) {
            $this->class->addDeclaration('declare(string_types=1);');
        }
    }
}