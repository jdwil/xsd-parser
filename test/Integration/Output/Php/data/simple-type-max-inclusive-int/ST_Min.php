<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\Ooxml\Spreadsheetml\Main;

use JDWil\Xsd\Test\Interfaces\SimpleTypeInterface;
use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\Interfaces\HasMaxInterface;

class ST_Min implements SimpleTypeInterface, HasMaxInterface
{
    /**
     * @var int
     */
    protected $value;

    /**
     * ST_Min constructor
     * @param int $value
     * @throws ValidationException
     */
    public function __construct(int $value)
    {
        $this->value = $value;

        if ($this->value > 1) {
            throw new ValidationException('value out of bounds');
        }
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
