<?php

namespace JDWil\Xsd\Integration\Output\Php;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Event\EventDispatcher;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\ClassBuilder;
use JDWil\Xsd\Output\Php\InterfaceGenerator;
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
     * @var InterfaceGenerator
     */
    private $interfaceGenerator;

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
        $this->interfaceGenerator = new InterfaceGenerator($this->options);
        $this->getProcessor = new ProcessorFactory($this->options, $this->definition, $this->interfaceGenerator);
    }

    public function testClassGeneration()
    {
        $parent = new \DirectoryIterator(sprintf('%s/data', __DIR__));
        foreach ($parent as $directory) {
            if ($directory->isDir() && !$directory->isDot()) {
                $document = new \DOMDocument();
                $document->load(sprintf('%s/data/%s/source.xsd', __DIR__, $directory->getFilename()));
                $this->definition = new Definition();
                $this->getProcessor = new ProcessorFactory($this->options, $this->definition, $this->interfaceGenerator);
                $dispatcher = EventDispatcher::forNormalization();
                $parser = new Parser($this->definition, $dispatcher);
                $parser->parse($document);

                $this->scanPhpFiles(sprintf('%s/data/%s', __DIR__, $directory->getFilename()));
            }
        }
    }

    private function scanPhpFiles(string $dir)
    {
        $testDir = new \DirectoryIterator($dir);
        foreach ($testDir as $file) {
            if (preg_match('/\.php$/', $file->getFilename())) {
                $pieces = explode('.', $file->getFilename());
                $className = array_shift($pieces);
                $pieces = explode('/', $file->getPath());
                $namespacePieces = [];
                for ($i = 0; $i < count($pieces); $i++) {
                    if ($pieces[$i] === 'data') {
                        for ($j = $i + 2; $j < count($pieces); $j++) {
                            $namespacePieces[] = $pieces[$j];
                        }
                        break;
                    }
                }
                if (!empty($namespacePieces)) {
                    $namespace = implode('/', $namespacePieces);
                    $element = $this->definition->findElementByName($className, $namespace);
                } else {
                    $element = $this->definition->findElementByName($className);
                }
                $processor = $this->getProcessor->forElement($element);
                $stream = $this->getOutputStream();
                /** @var ClassBuilder $class */
                $class = $processor->buildClass();
                $class->writeTo($stream);
                $this->assertStringEqualsFile(
                    $file->getPathname(),
                    $this->source,
                    sprintf('Failed in %s', $file->getPathname())
                );
            } else if (!$file->isDot() && $file->isDir()) {
                $this->scanPhpFiles($file->getPathname());
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
