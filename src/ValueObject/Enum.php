<?php
declare(strict_types=1);

namespace JDWil\Xsd\ValueObject;

/**
 * Class Enum
 * @package JDWil\Xsd\ValueObject
 */
class Enum
{
    /**
     * @var array
     */
    private $values;

    /**
     * Enum constructor.
     */
    public function __construct()
    {
        $this->values = [];
    }

    /**
     * @param $value
     */
    public function add($value)
    {
        $this->values = $value;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->values;
    }
}
