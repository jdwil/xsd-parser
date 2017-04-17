<?php
declare(strict_types=1);

namespace JDWil\Xsd\DOM;

class Definition
{
    /**
     * @var Schema[]
     */
    private $schemas;

    public function __construct()
    {
        $this->schemas = [];
    }

    public function addSchema(Schema $schema)
    {
        $this->schemas[$schema->getNamespace()] = $schema;
    }

    public function getTypes(): array
    {
        $ret = [];
        foreach ($this->schemas as $schema) {
            $ret = array_merge($ret, $schema->getTypes());
        }

        return $ret;
    }

    public function findType(string $name):? Type
    {
        foreach ($this->schemas as $schema) {
            if ($type = $schema->findType($name)) {
                return $type;
            }
        }

        return null;
    }

    public function getSchemaForNode(\DOMElement $node): Schema
    {
        $parent = $node->parentNode;
        return $this->schemas[Schema::determineNamespace($parent)];
    }

    public function schemaLoaded(string $namespace): bool
    {
        foreach ($this->schemas as $schema) {
            if ($schema->getNamespace() === $namespace) {
                return true;
            }
        }

        return false;
    }
}
