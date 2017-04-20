<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\Attribute;
use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Element\SimpleType;
use JDWil\Xsd\Exception\FileSystemException;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\Processor\ProcessorFactory;
use JDWil\Xsd\Stream\OutputStream;
use JDWil\Xsd\Util\TypeUtil;

/**
 * Class ClassGenerator
 * @package JDWil\Xsd\Output\Php
 */
class ClassGenerator
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
     * @var ProcessorFactory
     */
    private $getProcessor;

    /**
     * ClassGenerator constructor.
     * @param Options $options
     * @param Definition $definition
     * @param ProcessorFactory $factory
     */
    public function __construct(Options $options, Definition $definition, ProcessorFactory $factory)
    {
        $this->options = $options;
        $this->definition = $definition;
        $this->getProcessor = $factory;
    }

    public function generate()
    {
        $this->writeExceptions();
        foreach ($this->definition->getElements() as $element) {
            if ($processor = $this->getProcessor->forElement($element)) {
                $class = $processor->buildClass();
                $class->writeTo(OutputStream::streamedTo(
                    sprintf('%s/%s.php', $this->options->outputDirectory, $class->getClassName())
                ));
            }
        }
    }

    private function writeExceptions()
    {
        $path = sprintf('%s/Exception', $this->options->outputDirectory);
        if (!@mkdir($path, 0775) && !is_dir($path)) {
            throw new FileSystemException(sprintf('Could not create directory %s', $path));
        }

        $builder = new ClassBuilder($this->options);
        $builder
            ->setNamespace(sprintf('%s\\Exception', $this->options->namespacePrefix))
            ->setClassName('ValidationException')
            ->setClassExtends('\\Exception')
        ;

        if ($this->options->declareStrictTypes) {
            $builder->addDeclaration('declare(strict_types=1);');
        }

        $builder->writeTo(OutputStream::streamedTo(
            sprintf('%s/ValidationException.php', $path)
        ));
    }
}
