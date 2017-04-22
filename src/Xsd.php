<?php
declare(strict_types=1);

namespace JDWil\Xsd;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Event\EventDispatcher;
use JDWil\Xsd\Exception\DocumentException;
use JDWil\Xsd\Log\Logger;
use JDWil\Xsd\Log\LoggerInterface;
use JDWil\Xsd\Output\Php\ClassGenerator;
use JDWil\Xsd\Output\Php\Processor\ProcessorFactory;
use JDWil\Xsd\Parser\Parser;

class Xsd
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var \DOMDocument
     */
    private $document;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Options
     */
    private $options;

    /**
     * @param string $filePath
     * @param LoggerInterface|null $logger
     * @return Xsd
     */
    public static function forFile(string $filePath, LoggerInterface $logger = null): Xsd
    {
        $ret = new Xsd();
        $ret->source = $filePath;
        $ret->document = new \DOMDocument('1.0', 'UTF-8');
        $ret->logger = $logger ?? new Logger();

        return $ret;
    }

    /**
     * @param Options $options
     * @throws \JDWil\Xsd\Exception\DocumentException
     */
    public function generateCode(Options $options)
    {
        $this->options = $options;
        $this->loadDocument();
    }

    public function format(Options $options)
    {

    }

    public function dumpInfo()
    {
        $this->options = new Options();
        $this->options->namespacePrefix = 'JDWil\\Xsd\\Test';
        $this->options->outputDirectory = __DIR__ . '/../src/Test';
        $this->loadDocument();

        $definition = new Definition();
        $dispatcher = EventDispatcher::forNormalization();

        $parser = new Parser($definition, $dispatcher);
        $parser->parse($this->document);
        $factory = new ProcessorFactory($this->options, $definition);

        foreach ($definition->getElements() as $element) {
            if ($element instanceof ComplexType && $element->getName() === 'CT_Sheet') {
                $generator = new ClassGenerator($this->options, $definition, $factory);
                $generator->generate();
            }
        }
    }

    /**
     * @throws DocumentException
     */
    private function loadDocument()
    {
        if (!$this->document->load($this->source)) {
            throw new DocumentException(
                sprintf('Could not read file: %s', $this->source)
            );
        }

        if ($this->options->debug) {
            $this->logger->debug(sprintf('Loaded XSD document: %s', $this->source));
        }
    }
}
