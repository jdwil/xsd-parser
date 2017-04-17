<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class SimpleType
 * @package JDWil\Xsd\Element
 */
class SimpleType extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $name;

    /**
     * SimpleType constructor.
     * @param string|null $id
     * @param string|null $name
     */
    public function __construct(string $id = null, string $name = null)
    {
        $this->name = $name;
        parent::__construct($id);
    }
}