<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\Ooxml\Spreadsheetml\Main;

use JDWil\Xsd\Test\Interfaces\SimpleTypeInterface;
use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\Ooxml\OfficeDocument\SharedTypes\ST_FixedPercentage as sST_FixedPercentage;

class ST_FixedPercentage implements SimpleTypeInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * ST_FixedPercentage constructor
     * @param mixed $value
     * @throws ValidationException
     */
    public function __construct($value)
    {
        $this->value = $value;
        if (!$value instanceof sST_FixedPercentage) {
            throw new ValidationException('value is not valid for union.');
        }
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s', $this->value);
    }
}
