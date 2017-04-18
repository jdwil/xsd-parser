<?php
declare(strict_types=1);

namespace JDWil\Xsd;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\ElementInterface;
use JDWil\Xsd\Event\EventDispatcher;
use JDWil\Xsd\Exception\DocumentException;
use JDWil\Xsd\Log\Logger;
use JDWil\Xsd\Log\LoggerInterface;
use JDWil\Xsd\Parser\Normalize\FoundAllListener;
use JDWil\Xsd\Parser\Normalize\FoundAnnotationListener;
use JDWil\Xsd\Parser\Normalize\FoundAttributeGroupListener;
use JDWil\Xsd\Parser\Normalize\FoundAttributeListener;
use JDWil\Xsd\Parser\Normalize\FoundComplexTypeListener;
use JDWil\Xsd\Parser\Normalize\FoundExtensionListener;
use JDWil\Xsd\Parser\Normalize\FoundImportListener;
use JDWil\Xsd\Parser\Normalize\FoundSchemaListener;
use JDWil\Xsd\Parser\Normalize\FoundSimpleContentListener;
use JDWil\Xsd\Parser\Normalize\FoundSimpleTypeListener;
use JDWil\Xsd\Parser\Parser;
use JDWil\Xsd\Parser\XsdNormalizer;
use JDWil\Xsd\Stream\OutputStream;
use JDWil\Xsd\Translator\PlainTextTranslator;

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
        $this->loadDocument();

        $definition = new Definition();
        $dispatcher = new EventDispatcher();
        $dispatcher->registerListener(new FoundImportListener($dispatcher));
        $dispatcher->registerListener(new FoundSchemaListener());
        $dispatcher->registerListener(new FoundSimpleTypeListener());
        $dispatcher->registerListener(new FoundAttributeListener());
        $dispatcher->registerListener(new FoundAllListener());
        $dispatcher->registerListener(new FoundAnnotationListener());
        $dispatcher->registerListener(new FoundComplexTypeListener());
        $dispatcher->registerListener(new FoundAttributeGroupListener());
        $dispatcher->registerListener(new FoundExtensionListener());
        $dispatcher->registerListener(new FoundSimpleContentListener());

        $parser = new Parser($definition, $dispatcher);
        $parser->parse($this->document);
        /*
        $normalizer = new XsdNormalizer();
        $definition = $normalizer->normalize($this->document);
        */
        echo count($definition->getElements());
        //$translator = new PlainTextTranslator();
        //$translator->translate($this->document, OutputStream::streamedTo('php://stdout'));
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
