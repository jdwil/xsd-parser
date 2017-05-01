<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\AbstractElement;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\InterfaceGenerator;

/**
 * Class ProcessorFactory
 * @package JDWil\Xsd\Output\Php\Processor
 */
final class ProcessorFactory
{
    /**
     * @var Options
     */
    private $options;

    /**
     * @var Definition
     */
    private $definition;

    /**
     * @var InterfaceGenerator
     */
    private $interfaceGenerator;

    /**
     * ProcessorFactory constructor.
     * @param Options $options
     * @param Definition $definition
     * @param InterfaceGenerator $interfaceGenerator
     */
    public function __construct(Options $options, Definition $definition, InterfaceGenerator $interfaceGenerator)
    {
        $this->options = $options;
        $this->definition = $definition;
        $this->interfaceGenerator = $interfaceGenerator;
    }

    /**
     * @param AbstractElement $element
     * @return ProcessorInterface|null
     */
    public function forElement(AbstractElement $element)
    {
        $className = implode('', array_slice(explode('\\', get_class($element)), -1));
        $fqn = sprintf('JDWil\\Xsd\\Output\\Php\\Processor\\%sProcessor', $className);
        if (class_exists($fqn)) {
            return new $fqn($element, $this->options, $this->definition, $this->interfaceGenerator);
        }

        return null;
    }
}
