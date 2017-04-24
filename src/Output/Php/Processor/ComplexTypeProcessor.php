<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use Doctrine\Common\Inflector\Inflector;
use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\AbstractElement;
use JDWil\Xsd\Element\All;
use JDWil\Xsd\Element\Annotation;
use JDWil\Xsd\Element\Any;
use JDWil\Xsd\Element\AnyAttribute;
use JDWil\Xsd\Element\Attribute;
use JDWil\Xsd\Element\AttributeGroup;
use JDWil\Xsd\Element\Choice;
use JDWil\Xsd\Element\ComplexContent;
use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Element\Element;
use JDWil\Xsd\Element\Extension;
use JDWil\Xsd\Element\Group;
use JDWil\Xsd\Element\Restriction;
use JDWil\Xsd\Element\Sequence;
use JDWil\Xsd\Element\SimpleContent;
use JDWil\Xsd\Element\SimpleType;
use JDWil\Xsd\Exception\ClassNotFoundException;
use JDWil\Xsd\Options;
use JDWil\Xsd\Output\Php\ClassBuilder;
use JDWil\Xsd\Output\Php\Property;
use JDWil\Xsd\Util\TypeUtil;

/**
 * Class ComplexTypeProcessor
 * @package JDWil\Xsd\Output\Php\Processor
 */
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

    /**
     * @return ClassBuilder
     */
    public function buildClass()
    {
        $this->setClassDetails();
        $this->processClassAttributes($this->type);
        $this->createWriteXML();

        return $this->class;
    }

    protected function setClassDetails()
    {
        $this->class->setNamespace(sprintf('%s\\ComplexType', $this->options->namespacePrefix));
        $this->class->setClassName($this->type->getName());
        $this->initializeClass();
    }

    /**
     * @param AbstractElement $element
     */
    protected function processClassAttributes(AbstractElement $element)
    {
        foreach ($element->getChildren() as $child) {
            if ($child instanceof Annotation) {
                $this->processAnnotation($child, $this->class);
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

    /**
     * @param SimpleContent $simpleContent
     * @throws ClassNotFoundException
     */
    private function processSimpleContent(SimpleContent $simpleContent)
    {
        foreach ($simpleContent->getChildren() as $child) {
            if ($child instanceof Extension) {
                if (!$baseClass = $this->definition->findClass($child->getBase())) {
                    throw new ClassNotFoundException(sprintf('Could not find class for base %s', $child->getBase()));
                }
                $this->class = clone $baseClass;
                $this->setClassDetails();
                $this->processClassAttributes($child);
            } else if ($child instanceof Restriction) {
                $this->processRestriction($child);
            } else if ($child instanceof Annotation) {
                $this->processAnnotation($child, $this->class);
            }
        }
    }

    /**
     * @param ComplexContent $complexContent
     */
    private function processComplexContent(ComplexContent $complexContent)
    {
        foreach ($complexContent->getChildren() as $child) {
            if ($child instanceof Restriction) {
                $this->processRestriction($child);
            } else if ($child instanceof Extension) {
                $this->processExtension($child);
            } else if ($child instanceof Annotation) {
                $this->processAnnotation($child, $this->class);
            }
        }
    }

    /**
     * @param Extension $extension
     * @throws \JDWil\Xsd\Exception\TypeNotFoundException
     */
    private function processExtension(Extension $extension)
    {
        if ($base = $extension->getBase()) {
            $type = $this->resolveReference($base, $extension);
            if ($type instanceof ComplexType) {
                $this->processClassAttributes($type);
            } else if ($type instanceof SimpleType) {
                $this->extendSimpleType($type);
            }
        }

        $this->processClassAttributes($extension);
    }

    /**
     * @param Group $group
     */
    private function processGroup(Group $group)
    {
        if ($ref = $group->getRef()) {
            $group = $this->resolveReference($ref, $group);
        }

        foreach ($group->getChildren() as $child) {
            if ($child instanceof Sequence) {
                $this->processSequence($child);
            } else if ($child instanceof Choice) {
                $this->processChoice($child);
            } else if ($child instanceof All) {
                $this->processAll($child);
            } else if ($child instanceof Annotation) {
                $this->processAnnotation($child, $this->class);
            }
        }
    }

    /**
     * @param All $all
     * @throws \Exception
     */
    private function processAll(All $all)
    {
        foreach ($all->getChildren() as $child) {
            if ($child instanceof Element) {
                $this->processElement($child);
            } else if ($child instanceof Annotation) {
                $this->processAnnotation($child, $this->class);
            }
        }
    }

    /**
     * @param Choice $choice
     */
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

    /**
     * @param Element $element
     * @throws \JDWil\Xsd\Exception\FileSystemException
     */
    private function processElement(Element $element)
    {
        if ($type = $element->getType()) {

            $max = $element->getMaxOccurs() === 'unbounded' ? -1 : (int) $element->getMaxOccurs();

            /*
             * This is a collection
             */
            if ($max === -1 || $max > 1 || $element->getMinOccurs() > 1) {
                $namespace = sprintf(
                    '%s\\%s\\%s',
                    $this->options->namespacePrefix,
                    $this->getTypeNamespace($type),
                    $type
                );
                $class = $this->buildCollection($type, $namespace, $element->getMinOccurs(), $max);
                $this->class->uses(sprintf('use %s\\%s;', $class->getNamespace(), $class->getClassName()));
                foreach ($class->getUses() as $uses) {
                    $this->class->uses($uses);
                }

                $property = new Property();
                $property->name = Inflector::pluralize($element->getName());
                $collection = sprintf('%sCollection', $type);
                $property->type = $collection;
                $property->default = $collection;
                $property->createGetter = false;
                $property->isCollection = true;
                $property->collectionOf = $element->getType();
                $property->isAttribute = false;
                $this->class->addProperty($property);
            } else { // Not a collection
                $property = new Property();
                $property->name = $element->getName();
                $property->type = $element->getType();
                $property->isAttribute = false;
                if ($element->getMinOccurs() === 0) {
                    $property->includeInConstructor = false;
                }
                $this->class->addProperty($property);
            }

        }
    }

    /**
     * @param Sequence $sequence
     */
    private function processSequence(Sequence $sequence)
    {
        foreach ($sequence->getChildren() as $child) {
            if ($child instanceof Element) {
                $this->processElement($child);
            } else if ($child instanceof Group) {
                $this->processGroup($child);
            } else if ($child instanceof Choice) {
                $this->processChoice($child);
            } else if ($child instanceof Sequence) {
                $this->processSequence($child);
            } else if ($child instanceof Any) {
                $this->processAny($child);
            } else if ($child instanceof Annotation) {
                $this->processAnnotation($child, $this->class);
            }
        }
    }

    /**
     * @param Any $any
     */
    private function processAny(Any $any)
    {
        $property = new Property();
        $property->name = 'any';
        $property->type = 'mixed';
        $property->collectionMin = $any->getMinOccurs();
        $property->collectionMax = $any->getMaxOccurs();
        if ($any->getMinOccurs() > 1 || $any->getMaxOccurs() === 'unbounded' || (int) $any->getMaxOccurs() > 1) {
            $property->isCollection = true;
            $property->collectionOf = 'Any';
        }

        $this->class->addProperty($property);

        /*
         * @todo this is incomplete. Namespaces need to be checked and we need an AnyCollection.
         */
    }

    /**
     * @param Attribute $attribute
     * @throws \JDWil\Xsd\Exception\FileSystemException
     */
    private function processAttribute(Attribute $attribute)
    {
        if ($ref = $attribute->getRef()) {
            $attribute = $this->resolveReference($ref, $attribute);
        }

        if (!$attribute || $attribute->getUse() === Attribute::USE_PROHIBITED) {
            return;
        }

        $property = Property::fromAttribute($attribute);
        if (!$property->isPrimitive()) {
            $typeName = $typeNs = $ns = '';
            $this->analyzeType($attribute->getType(), $typeName, $typeNs, $ns);
            $property->type = $typeName;
            if (!empty($typeNs)) {
                $this->class->uses(sprintf('use %s\\%s\\%s;', $this->options->namespacePrefix, $typeNs, $property->type));
            }
        }
        $this->class->addProperty($property);
    }

    /**
     * @param AttributeGroup $attributeGroup
     * @throws \Exception
     */
    private function processAttributeGroup(AttributeGroup $attributeGroup)
    {
        if ($ref = $attributeGroup->getRef()) {
            $attributeGroup = $this->resolveReference($ref, $attributeGroup);
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
                $this->processAnnotation($child, $this->class);
            }
        }
    }

    /**
     * @param AnyAttribute $anyAttribute
     * @throws \Exception
     */
    private function processAnyAttribute(AnyAttribute $anyAttribute)
    {
        // @todo implement this
        throw new \Exception('anyAttribute within complexType is not supported yet.');
    }
}
