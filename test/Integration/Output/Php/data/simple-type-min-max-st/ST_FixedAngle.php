<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\Ooxml\Spreadsheetml\Main;

use JDWil\Xsd\Test\Interfaces\SimpleTypeInterface;
use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\Ooxml\Spreadsheetml\Main\ST_Angle;
use JDWil\Xsd\Test\Interfaces\HasMinInterface;
use JDWil\Xsd\Test\Interfaces\HasMaxInterface;

class ST_FixedAngle implements SimpleTypeInterface, HasMinInterface, HasMaxInterface
{
    /**
     * @var ST_Angle
     */
    protected $value;

    /**
     * ST_FixedAngle constructor
     * @param ST_Angle $value
     * @throws ValidationException
     */
    public function __construct(ST_Angle $value)
    {
        $this->value = $value;

        if ($this->value <= -5400000) {
            throw new ValidationException('value out of bounds');
        }

        if ($this->value >= 5400000) {
            throw new ValidationException('value out of bounds');
        }
    }

    /**
     * @return ST_Angle
     */
    public function getValue(): ST_Angle
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
