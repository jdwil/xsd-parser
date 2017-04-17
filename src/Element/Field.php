<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Field
 * @package JDWil\Xsd\Element
 */
class Field extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $xpath;

    /**
     * Field constructor.
     * @param string $xpath
     * @param string|null $id
     */
    public function __construct(string $xpath, string $id = null)
    {
        $this->xpath = $xpath;
        parent::__construct($id);
    }
}