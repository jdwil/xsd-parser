<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use JDWil\Xsd\Exception\FileSystemException;
use JDWil\Xsd\Options;
use JDWil\Xsd\Stream\OutputStream;
use JDWil\Xsd\Util\NamespaceUtil;

class InterfaceGenerator
{
    const INTERFACE_NAMESPACE = 'Interfaces';
    const TYPE_SIMPLE_TYPE = 'SimpleTypeInterface';
    const TYPE_COMPLEX_TYPE = 'ComplexTypeInterface';
    const TYPE_ENUM = 'EnumInterface';
    const TYPE_MIN = 'HasMinInterface';
    const TYPE_MAX = 'HasMaxInterface';
    const TYPE_LENGTH = 'HasLengthInterface';
    const TYPE_PATTERN = 'HasPatternInterface';
    const TYPE_WHITESPACE = 'HasWhitespaceRuleInterface';

    /**
     * @var Options
     */
    private $options;

    /**
     * @var string[]
     */
    private $generatedInterfaces;

    /**
     * InterfaceGenerator constructor.
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
        $this->generatedInterfaces = [];
    }

    /**
     * @param string $type
     * @throws \JDWil\Xsd\Exception\ValidationException
     * @throws \JDWil\Xsd\Exception\FileSystemException
     */
    public function generateInterface(string $type)
    {
        if (in_array($type, $this->generatedInterfaces, true)) {
            return;
        }

        $outputDirectory = NamespaceUtil::outputDirectory($this->options, self::INTERFACE_NAMESPACE);
        // @todo move this somewhere more appropriate.
        if (!@mkdir($outputDirectory) && !is_dir($outputDirectory)) {
            echo "\n" . $outputDirectory . "\n";
            throw new FileSystemException('Could not create directory');
        }

        $class = new ClassBuilder($this->options);
        $class->setClassType(ClassBuilder::TYPE_INTERFACE);
        $class->setClassName($type);
        $class->setNamespace(NamespaceUtil::classNamespace($this->options, self::INTERFACE_NAMESPACE));
        $class->writeTo(OutputStream::streamedTo(
            sprintf('%s/%s.php', $outputDirectory, $type)
        ));
        $this->generatedInterfaces[] = $type;
    }
}
