<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Unique
 * @package JDWil\Xsd\Element
 */
class Unique extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Unique constructor.
     * @param string $name
     * @param string|null $id
     */
    public function __construct(string $name, string $id = null)
    {
        $this->name = $name;
        parent::__construct($id);
    }
}