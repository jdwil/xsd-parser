<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Selector
 * @package JDWil\Xsd\Element
 */
class Selector extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $xpath;

    /**
     * Selector constructor.
     * @param string $xpath
     * @param string|null $id
     */
    public function __construct(string $xpath, string $id = null)
    {
        $this->xpath = $xpath;
        parent::__construct($id);
    }
}