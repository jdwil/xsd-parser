<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Import
 * @package JDWil\Xsd\Element
 */
class Import extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $schemaLocation;

    /**
     * Import constructor.
     * @param string|null $id
     * @param string|null $namespace
     * @param string|null $schemaLocation
     */
    public function __construct(string $id = null, string $namespace = null, string $schemaLocation = null)
    {
        $this->namespace = $namespace;
        $this->schemaLocation = $schemaLocation;
        parent::__construct($id);
    }
}