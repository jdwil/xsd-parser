<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class XInclude
 * @package JDWil\Xsd\Element
 */
class XInclude extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $schemaLocation;

    /**
     * XInclude constructor.
     * @param string $schemaLocation
     * @param string|null $id
     */
    public function __construct(string $schemaLocation, string $id = null)
    {
        $this->schemaLocation = $schemaLocation;
        parent::__construct($id);
    }
}