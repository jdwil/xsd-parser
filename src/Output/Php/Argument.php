<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;


/**
 * Class Argument
 * @package JDWil\Xsd\Output\Php
 */
class Argument
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var mixed
     */
    public $default;

    /**
     * Argument constructor.
     * @param string $name
     * @param string $type
     * @param null $default
     */
    public function __construct(string $name, string $type, $default = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->default = $default;
    }
}