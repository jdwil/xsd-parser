<?php
declare(strict_types=1);

namespace JDWil\Xsd\DOM;

use JDWil\Xsd\Element\Schema;

/**
 * Class Definition
 * @package JDWil\Xsd\DOM
 */
class Definition
{
    /**
     * @var Schema[]
     */
    private $schemas;

    /**
     * Definition constructor.
     */
    public function __construct()
    {
        $this->schemas = [];
    }

    /**
     * @param Schema $schema
     */
    public function addSchema(Schema $schema)
    {
        $this->schemas[$schema->getNamespace()] = $schema;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        $ret = [];
        foreach ($this->schemas as $schema) {
            $ret = array_merge($ret, $schema->getTypes());
        }

        return $ret;
    }

    /**
     * @param string $name
     * @return Type|null
     */
    public function findType(string $name)
    {
        foreach ($this->schemas as $schema) {
            if ($type = $schema->findType($name)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @param \DOMElement $node
     * @return Schema
     */
    public function getSchemaForNode(\DOMElement $node): Schema
    {
        $parent = $node->parentNode;
        return $this->schemas[Schema::determineNamespace($parent)];
    }

    /**
     * @param string $namespace
     * @return bool
     */
    public function schemaLoaded(string $namespace): bool
    {
        foreach ($this->schemas as $schema) {
            if ($schema->getNamespace() === $namespace) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \DOMNode $node
     * @return Type|null
     */
    public function findObjectForNode(\DOMNode $node)
    {
        foreach ($this->schemas as $schema) {
            if ($schema->isForNode($node)) {
                return $schema;
            }

            foreach ($schema->getTypes() as $type) {
                /** @var Type $type */
                if ($type->isForNode($node)) {
                    return $type;
                }
            }
        }

        return null;
    }
}
