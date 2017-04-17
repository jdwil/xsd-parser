<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Redefine
 * @package JDWil\Xsd\Element
 */
class Redefine extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $schemaLocation;

    /**
     * Redefine constructor.
     * @param string $schemaLocation
     * @param string|null $id
     */
    public function __construct(string $schemaLocation, string $id = null)
    {
        $this->schemaLocation = $schemaLocation;
        parent::__construct($id);
    }
}