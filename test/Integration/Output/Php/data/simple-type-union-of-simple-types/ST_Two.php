<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Exception\ValidationException;

class ST_Two
{
    /**
     * @var string
     */
    protected $value;

    /**
     * ST_Two constructor
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
