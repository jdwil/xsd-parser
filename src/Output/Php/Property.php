<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

/**
 * Class Property
 * @package JDWil\Xsd\Output\Php
 */
class Property
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
     * @var bool
     */
    public $required = false;

    /**
     * @var bool
     */
    public $fixed = false;

    /**
     * @var array
     */
    public $enumerations = [];

    /**
     * @var bool
     */
    public $immutable = false;

    /**
     * @param string $value
     */
    public function addEnumeration(string $value)
    {
        $this->enumerations[] = $value;
    }
}
