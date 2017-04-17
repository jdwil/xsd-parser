<?php
declare(strict_types=1);

namespace JDWil\Xsd\Parser;

use JDWil\Xsd\DOM\Attribute;
use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\DOM\Schema;
use JDWil\Xsd\DOM\SimpleType;
use JDWil\Xsd\DOM\Type;
use JDWil\Xsd\Exception\DocumentException;

class XsdNormalizer
{
    private $definition;

    /**
     * @var string[]
     */
    private $importedSchemas;

    public function __construct()
    {
        $this->definition = new Definition();
        $this->importedSchemas = [];
    }

    public function normalize(\DOMDocument $document): Definition
    {
        $this->processDomElement($document->documentElement);

        return $this->definition;
    }

    protected function processDomElement(\DOMElement $node)
    {
        switch ($node->localName) {
            case 'schema':
                $this->processSchema($node);
                break;

            case 'import':
                $this->importSchema($node);
                break;

            case 'simpleType':
                $this->processSimpleType($node);
                break;

            case 'attribute':
                $this->processAttribute($node);
                break;

            case 'complexType':
                break;

            case 'element':
                break;

            case 'attributeGroup':
                break;

            case 'group':
                break;

            default:
                var_dump($node);
                throw new DocumentException(
                    sprintf('Encountered unknown tag name: %s', $node->localName)
                );
        }
    }

    protected function processAttribute(\DOMElement $node)
    {
        /**
         * This is a globally-defined attribute.
         */
        if ($node->parentNode->localName === 'schema') {
            $name = $node->getAttribute('name');
            if ($baseType = $this->definition->findType($node->getAttribute('type'))) {
                $baseType = $baseType->getBaseType();
            } else {
                $baseType = $node->getAttribute('type');
            }

            $this->definition->getSchemaForNode($node)->addAttribute(new Attribute($name, $baseType));
        } else {

        }
    }

    protected function processSimpleType(\DOMElement $node)
    {
        $name = $node->getAttribute('name');
        $baseType = Type::TYPE_UNKNOWN;
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                /** @var \DOMElement $childNode */
                if ($childNode->localName === 'restriction') {
                    $baseType = str_replace('xsd:', '', $childNode->getAttribute('base'));
                }
            }
        }

        $this->definition->getSchemaForNode($node)->addType(new SimpleType($name, $baseType));
    }

    protected function processSchema(\DOMElement $node)
    {
        $namespace = Schema::determineNamespace($node);

        if ($this->definition->schemaLoaded($namespace)) {
            return;
        }

        $schema = Schema::forNamespace($namespace);
        $this->definition->addSchema($schema);

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                if ($childNode instanceof \DOMElement) {
                    $this->processDomElement($childNode);
                } else if ($childNode instanceof \DOMText) {
                    // TODO Handle this type
                } else {
                    echo get_class($childNode) . "\n";
                    die('what is this?');
                }
            }
        }
    }

    protected function importSchema(\DOMElement $node)
    {
        $location = $node->getAttribute('schemaLocation');

        if (in_array($location, $this->importedSchemas, true)) {
            return;
        }

        $baseUri = $node->baseURI;
        $pieces = explode('/', $baseUri);
        array_pop($pieces);
        $pieces[] = $location;
        $uri = implode('/', $pieces);

        $document = new \DOMDocument('1.0', 'UTF-8');
        if (!$document->load($uri)) {
            throw new DocumentException(
                sprintf('Could not load schema file: %s', $uri)
            );
        }
        $this->importedSchemas[] = $location;

        $this->processSchema($document->documentElement);
    }
}
