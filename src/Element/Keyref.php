<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Keyref
 * @package JDWil\Xsd\Element
 */
class Keyref extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $refer;

    /**
     * Keyref constructor.
     * @param string $name
     * @param string $refer
     * @param string|null $id
     */
    public function __construct(string $name, string $refer, string $id = null)
    {
        $this->name = $name;
        $this->refer = $refer;
        parent::__construct($id);
    }
}