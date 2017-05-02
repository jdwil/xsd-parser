<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\Ooxml\Spreadsheetml\Main;

use JDWil\Xsd\Test\Interfaces\SimpleTypeInterface;
use JDWil\Xsd\Test\Exception\ValidationException;

class ST_Angle implements SimpleTypeInterface
{
    /**
     * @var int
     */
    protected $value;

    /**
     * ST_Angle constructor
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%d', $this->value);
    }
}
