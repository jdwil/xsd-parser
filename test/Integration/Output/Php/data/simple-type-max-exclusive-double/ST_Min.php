<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\Ooxml\Spreadsheetml\Main;

use JDWil\Xsd\Test\Interfaces\SimpleTypeInterface;
use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\Interfaces\HasMaxInterface;

class ST_Min implements SimpleTypeInterface, HasMaxInterface
{
    /**
     * @var double
     */
    protected $value;

    /**
     * ST_Min constructor
     * @param double $value
     * @throws ValidationException
     */
    public function __construct(double $value)
    {
        $this->value = $value;

        if ($this->value >= 0.000000) {
            throw new ValidationException('value out of bounds');
        }
    }

    /**
     * @return double
     */
    public function getValue(): double
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%f', $this->value);
    }
}
