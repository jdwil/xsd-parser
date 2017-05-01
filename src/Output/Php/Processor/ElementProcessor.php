<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use Doctrine\Common\Inflector\Inflector;
use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\Element;
use JDWil\Xsd\Element\Schema;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\Argument;
use JDWil\Xsd\Output\Php\ClassBuilder;
use JDWil\Xsd\Output\Php\InterfaceGenerator;
use JDWil\Xsd\Output\Php\Method;
use JDWil\Xsd\Util\NamespaceUtil;

/**
 * Class ElementProcessor
 * @package JDWil\Xsd\Output\Php\Processor
 */
class ElementProcessor extends AbstractProcessor
{
    /**
     * @var Element
     */
    private $type;

    /**
     * @var string
     */
    private $tagName;

    /**
     * SimpleTypeProcessor constructor.
     * @param Element $element
     * @param Options $options
     * @param Definition $definition
     * @param InterfaceGenerator $interfaceGenerator
     */
    public function __construct(
        Element $element,
        Options $options,
        Definition $definition,
        InterfaceGenerator $interfaceGenerator
    ) {
        $this->type = $element;
        parent::__construct($options, $definition, $interfaceGenerator);
    }

    public function buildClass()
    {
        if (!$this->type->getParent() instanceof Schema) {
            return null;
        }

        $this->initializeClass();
        $this->processClassAttributes();
        $this->class->setNamespace(sprintf('%s\\Element', $this->options->namespacePrefix));
        $this->createWriteXML();

        return $this->class;
    }

    private function processClassAttributes()
    {
        if ($ref = $this->type->getRef()) {
            list($ns, $name) = $this->definition->determineNamespace($ref, $this->type);
            $element = $this->definition->findElementByName($name, $ns);
        } else {
            $element = $this->type;
        }

        $this->class->setClassName(Inflector::classify($element->getName()));
        $this->tagName = $element->getName();
        if ($type = $element->getType()) {
            $ns = $this->getTypeNamespace($type);
            $this->class->setClassExtends($type);
            $this->class->uses(NamespaceUtil::classNamespace($this->options, $ns, $type));
        }
    }

    protected function createWriteXML()
    {
        $this->usesOutputStream();
        $method = new Method();
        $method->name = 'writeXMLTo';
        $method->addArgument(new Argument('stream', 'OutputStream'));
        $method->body = sprintf('        parent::writeXML($stream, \'%s\');', $this->tagName);
        $this->class->addMethod($method);
    }
}
