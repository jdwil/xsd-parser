<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\Attribute;
use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Element\SimpleType;
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
     * @var array
     */
    private $constructorArgs;

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
        $this->constructorArgs = [];
    }

    public function generate()
    {
        foreach ($this->definition->getElements() as $element) {
            if ($processor = $this->getProcessor->forElement($element)) {
                $class = $processor->buildClass();
                $class->writeTo(OutputStream::streamedTo(
                    sprintf('%s/%s.php', $this->options->outputDirectory, $class->getClassName())
                ));
            }
        }
    }

    /**
     * @param ComplexType $type
     */
    protected function generateComplexType(ComplexType $type)
    {
        $builder = new ClassBuilder($this->options);
        $builder
            ->setClassName($type->getName())
            ->setNamespace('Foo\\Bar')
            ->addDeclaration('declare(strict_types=1);')
        ;

        foreach ($type->getChildren() as $child) {
            if ($child instanceof Attribute) {
                if ($child->getRef()) {
                    // @todo find reference
                } else {

                    $dataType = $child->getType();
                    if ($localType = TypeUtil::typeToPhpPrimitive($dataType)) {
                        $dataType = $localType;
                    } else if (strpos($dataType, ':') !== false) {
                        list($ns, $localType) = explode(':', $dataType);
                        $ns = $type->getSchema()->findNamespaceByAlias($ns);
                        $localType = $this->definition->findElementByName($localType, $ns);
                        if ($localType instanceof SimpleType && $primitive = $localType->canBeMappedToPrimitive()) {
                            $dataType = $primitive;
                        }
                    } else if (!TypeUtil::isPrimitive($dataType)) {
                        $localType = $this->definition->findElementByName($dataType);
                        if ($localType instanceof SimpleType) {
                            $enums = $localType->isEnum();
                        }
                    }

                    $property = $builder
                        ->property()
                        ->setName($child->getName())
                        ->setType($dataType)
                        ->setDefault($child->getDefault())
                        ->setFixed((bool)$child->getFixed())
                        ->setRequired($child->getUse() === 'required')
                    ;

                    if (isset($enums) && is_array($enums)) {
                        foreach ($enums as $enum) {
                            $property->addEnumeration($enum);
                        }
                    }

                    $builder->addProperty($property->getProperty());
                }
            }
        }
        $builder->writeTo(OutputStream::streamedTo('./test-dir/test.php'));
    }
}
