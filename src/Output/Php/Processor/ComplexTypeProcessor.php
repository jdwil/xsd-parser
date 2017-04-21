<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use Doctrine\Common\Inflector\Inflector;
use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\All;
use JDWil\Xsd\Element\Annotation;
use JDWil\Xsd\Element\AnyAttribute;
use JDWil\Xsd\Element\Attribute;
use JDWil\Xsd\Element\AttributeGroup;
use JDWil\Xsd\Element\Choice;
use JDWil\Xsd\Element\ComplexContent;
use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Element\Element;
use JDWil\Xsd\Element\Group;
use JDWil\Xsd\Element\Sequence;
use JDWil\Xsd\Element\SimpleContent;
use JDWil\Xsd\Element\SimpleType;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\ClassBuilder;
use JDWil\Xsd\Output\Php\Property;
use JDWil\Xsd\Util\TypeUtil;

class ComplexTypeProcessor extends AbstractProcessor
{
    /**
     * @var ComplexType
     */
    private $type;

    /**
     * SimpleTypeProcessor constructor.
     * @param ComplexType $element
     * @param Options $options
     * @param Definition $definition
     */
    public function __construct(
        ComplexType $element,
        Options $options,
        Definition $definition
    ) {
        $this->type = $element;
        parent::__construct($options, $definition);
    }

    public function buildClass()
    {
        $this->class->setNamespace(sprintf('%s\\ComplexType', $this->options->namespacePrefix));
        $this->class->setClassName($this->type->getName());
        $this->initializeClass();
        $this->processClassAttributes();

        return $this->class;
    }

    protected function processClassAttributes()
    {
        foreach ($this->type->getChildren() as $child) {
            if ($child instanceof Annotation) {
                $this->processAnnotation($child);
            } else if ($child instanceof SimpleContent) {
                $this->processSimpleContent($child);
            } else if ($child instanceof ComplexContent) {
                $this->processComplexContent($child);
            } else if ($child instanceof Group) {
                $this->processGroup($child);
            } else if ($child instanceof All) {
                $this->processAll($child);
            } else if ($child instanceof Choice) {
                $this->processChoice($child);
            } else if ($child instanceof Sequence) {
                $this->processSequence($child);
            } else if ($child instanceof Attribute) {
                $this->processAttribute($child);
            } else if ($child instanceof AttributeGroup) {
                $this->processAttributeGroup($child);
            } else if ($child instanceof AnyAttribute) {
                $this->processAnyAttribute($child);
            }
        }
    }

    private function processAnnotation(Annotation $annotation)
    {
        // @todo implement this
        throw new \Exception('annotation within complexType is not supported yet.');
    }

    private function processSimpleContent(SimpleContent $simpleContent)
    {
        // @todo implement this
        throw new \Exception('simpleContent within complexType is not supported yet.');
    }

    private function processComplexContent(ComplexContent $complexContent)
    {
        // @todo implement this
        throw new \Exception('complexContent within complexType is not supported yet.');
    }

    private function processGroup(Group $group)
    {
        // @todo implement this
        throw new \Exception('group within complexType is not supported yet.');
    }

    private function processAll(All $all)
    {
        // @todo implement this
        throw new \Exception('all within complexType is not supported yet.');
    }

    private function processChoice(Choice $choice)
    {
        if (!$choiceGroup = $choice->getId()) {
            $choiceGroup = md5(uniqid('', true));
        }

        foreach ($choice->getChildren() as $child) {
            if ($child instanceof Element) {
                $element = $child;
                if ($ref = $child->getRef()) {
                    $element = $this->resolveReference($ref, $child);
                }
                $this->usesValidationException();

                $type = $classNs = $ns = '';
                $this->analyzeType($element->getType(), $type, $classNs, $ns);
                $property = Property::fromElement($element, $type, $classNs, $choiceGroup);

                $this->class->addProperty($property);
            }
        }
    }

    private function processSequence(Sequence $sequence)
    {
        foreach ($sequence->getChildren() as $child) {
            if ($child instanceof Element) {
                if ($type = $child->getType()) {

                    $max = $child->getMaxOccurs() === 'unbounded' ? -1 : (int) $child->getMaxOccurs();
                    if ($max == -1 || $max > 1 || $child->getMinOccurs() > 1) {
                        $namespace = sprintf(
                            '%s\\%s\\%s',
                            $this->options->namespacePrefix,
                            $this->getTypeNamespace($type),
                            $type
                        );
                        $class = $this->buildCollection($type, $namespace, $child->getMinOccurs(), $max);
                        $this->class->uses(sprintf('use %s\\%s;', $class->getNamespace(), $class->getClassName()));
                        foreach ($class->getUses() as $uses) {
                            $this->class->uses($uses);
                        }

                        $property = new Property();
                        $property->name = Inflector::pluralize($child->getName());
                        $collection = sprintf('%sCollection', $type);
                        $property->type = $collection;
                        $property->default = $collection;
                        $property->fixed = true;
                        $property->immutable = true;
                        $property->createGetter = false;
                        $this->class->addProperty($property);

                        foreach ($class->getMethods() as $classMethod) {
                            $method = clone $classMethod;
                            if ($method->name === 'add') {
                                $method->name = sprintf('add%s', Inflector::classify($child->getName()));
                                $body = <<<_BODY_
        \$this->{$property->name}->add(\$item);
_BODY_;
                                $method->body = $body;
                            } else {
                                $method->name = sprintf('all%s', Inflector::classify($child->getName()));
                                $body = <<<_BODY_
        return \$this->{$property->name}->all();
_BODY_;
                                $method->body = $body;

                            }

                            $this->class->addMethod($method);
                        }
                    } else {
                        $property = new Property();
                        $property->name = $child->getName();
                        $property->type = $child->getType();
                        if ($child->getMinOccurs() === 0) {
                            $property->includeInConstructor = false;
                        }
                        $this->class->addProperty($property);
                    }
                }
            }
        }
    }

    private function processAttribute(Attribute $attribute)
    {
        if ($ref = $attribute->getRef()) {
            list($namespace, $name) = $this->definition->determineNamespace($ref, $attribute);
            $attribute = $this->definition->findElementByName($name, $namespace);
        }

        if (!$attribute || $attribute->getUse() === Attribute::USE_PROHIBITED) {
            return;
        }

        $property = Property::fromAttribute($attribute);
        if (!$property->isPrimitive()) {
            list($ns, $name) = $this->definition->determineNamespace($attribute->getType(), $attribute);
            $typeNs = $this->getTypeNamespace($name, $ns);
            if (strlen($typeNs)) {
                $this->class->uses(sprintf('use %s\\%s\\%s;', $this->options->namespacePrefix, $typeNs, $property->type));
            }
        }
        $this->class->addProperty($property);
    }

    private function processAttributeGroup(AttributeGroup $attributeGroup)
    {
        if ($ref = $attributeGroup->getRef()) {
            list($ns, $name) = $this->definition->determineNamespace($ref, $attributeGroup);
            $attributeGroup = $this->definition->findElementByName($name, $ns);
        }

        foreach ($attributeGroup->getChildren() as $child) {
            if ($child instanceof Attribute) {
                $this->processAttribute($child);
            } else if ($child instanceof AttributeGroup) {
                $this->processAttributeGroup($child);
            } else if ($child instanceof AnyAttribute) {
                // @todo implement this
                throw new \Exception('anyAttribute is not yet supported.');
            } else if ($child instanceof Annotation) {
                // @todo implement this
                throw new \Exception('annotation is not yet supported.');
            }
        }
    }

    private function processAnyAttribute(AnyAttribute $anyAttribute)
    {
        // @todo implement this
        throw new \Exception('anyAttribute within complexType is not supported yet.');
    }
}
