<?php

namespace JDWil\Xsd\Integration\Output\Php;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Event\EventDispatcher;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\ClassBuilder;
use JDWil\Xsd\Output\Php\Processor\ProcessorFactory;
use JDWil\Xsd\Parser\Parser;
use JDWil\Xsd\Stream\OutputStream;
use JDWil\Xsd\Stream\OutputStreamInterface;
use PHPUnit\Framework\TestCase;

class ClassGenerationTest extends TestCase
{
    /**
     * @var ProcessorFactory
     */
    private $getProcessor;

    /**
     * @var Definition
     */
    private $definition;

    /**
     * @var Options
     */
    private $options;

    /**
     * @var string
     */
    public $source;

    public function setUp()
    {
        $this->options = new Options();
        $this->options->namespacePrefix = 'JDWil\\Xsd\\Test';
        $this->options->outputDirectory = sprintf('%s/Output', __DIR__);
        $this->definition = new Definition();
        $this->getProcessor = new ProcessorFactory($this->options, $this->definition);
    }

    public function testClassGeneration()
    {
        $parent = new \DirectoryIterator(sprintf('%s/data', __DIR__));
        foreach ($parent as $directory) {
            if ($directory->isDir() && !$directory->isDot()) {
                $document = new \DOMDocument();
                $document->load(sprintf('%s/data/%s/source.xsd', __DIR__, $directory->getFilename()));
                $this->definition = new Definition();
                $this->getProcessor = new ProcessorFactory($this->options, $this->definition);
                $dispatcher = EventDispatcher::forNormalization();
                $parser = new Parser($this->definition, $dispatcher);
                $parser->parse($document);

                $testDir = new \DirectoryIterator(sprintf('%s/data/%s', __DIR__, $directory->getFilename()));
                foreach ($testDir as $file) {
                    if (preg_match('/\.php$/', $file->getFilename())) {
                        $pieces = explode('.', $file->getFilename());
                        $className = array_shift($pieces);
                        $element = $this->definition->findElementByName($className);
                        $processor = $this->getProcessor->forElement($element);
                        $stream = $this->getOutputStream();
                        /** @var ClassBuilder $class */
                        $class = $processor->buildClass();
                        $class->writeTo($stream);
                        $this->assertStringEqualsFile(
                            sprintf('%s/data/%s/%s', __DIR__, $directory->getFilename(), $file->getFilename()),
                            $this->source,
                            sprintf('Failed in %s', $directory->getFilename())
                        );
                    }
                }
            }
        }
    }

    private function getOutputStream()
    {
        $this->source = '';
        $stream = $this->createMock(OutputStreamInterface::class);
        $test = $this;
        $stream
            ->expects($this->any())
            ->method('write')
            ->will($this->returnCallback(function ($data) use ($test) {
                $test->source .= $data;
            }))
        ;

        $stream
            ->expects($this->any())
            ->method('writeLine')
            ->will($this->returnCallback(function ($data) use ($test) {
                $test->source .= sprintf("%s\n", $data);
            }))
        ;

        return $stream;
    }
}
