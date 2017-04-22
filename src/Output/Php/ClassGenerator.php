<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\Attribute;
use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Element\Element;
use JDWil\Xsd\Element\ElementInterface;
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
        $streamDir = sprintf('%s/Stream', $this->options->outputDirectory);
        $this->createDirectory($streamDir);
        $sourceDir = __DIR__ . '/../../Stream';
        $fileTarget = sprintf('%s/OutputStream.php', $sourceDir);
        $source = file_get_contents($fileTarget);
        $newNamespace = sprintf('namespace %s\\Stream;', $this->options->namespacePrefix);
        $source = preg_replace('/^namespace [^;]+;/m', $newNamespace, $source);
        $stream = OutputStream::streamedTo(sprintf('%s/OutputStream.php', $streamDir));
        $stream->write($source);

        $this->writeExceptions();
        foreach ($this->definition->getElements() as $element) {
            if ($processor = $this->getProcessor->forElement($element)) {
                /** @var ClassBuilder $class */
                if (!$class = $processor->buildClass()) {
                    continue;
                }
                $this->definition->addClass($class, $class->getClassName());

                $path = $this->options->outputDirectory;
                if ($element instanceof SimpleType) {
                    $path = sprintf('%s/SimpleType', $this->options->outputDirectory);
                    $this->createDirectory($path);
                } else if ($element instanceof ComplexType) {
                    $path = sprintf('%s/ComplexType', $this->options->outputDirectory);
                    $this->createDirectory($path);
                } else if ($element instanceof Element) {
                    $path = sprintf('%s/Element', $this->options->outputDirectory);
                    $this->createDirectory($path);
                }

                $class->writeTo(OutputStream::streamedTo(
                    sprintf('%s/%s.php', $path, $class->getClassName())
                ));
            }
        }
    }

    private function createDirectory(string $path) {
        if (!@mkdir($path) && !is_dir($path)) {
            throw new FileSystemException(sprintf('Could not create directory %s', $path));
        }
    }

    private function writeExceptions()
    {
        $path = sprintf('%s/Exception', $this->options->outputDirectory);
        $this->createDirectory($path);

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
