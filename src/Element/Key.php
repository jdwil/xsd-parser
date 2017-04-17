<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Key
 * @package JDWil\Xsd\Element
 */
class Key extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Key constructor.
     * @param string $name
     * @param string|null $id
     */
    public function __construct(string $name, string $id = null)
    {
        $this->name = $name;
        parent::__construct($id);
    }
}