<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Notation
 * @package JDWil\Xsd\Element
 */
class Notation extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $public;

    /**
     * @var string
     */
    protected $system;

    /**
     * Notation constructor.
     * @param string $name
     * @param string $public
     * @param string|null $id
     * @param string|null $system
     */
    public function __construct(string $name, string $public, string $id = null, string $system = null)
    {
        $this->name = $name;
        $this->public = $public;
        $this->system = $system;
        parent::__construct($id);
    }
}