<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Interfaces\SimpleTypeInterface;
use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\SimpleType\ST_One;
use JDWil\Xsd\Test\SimpleType\ST_Two;

class ST_Union implements SimpleTypeInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * ST_Union constructor
     * @param mixed $value
     * @throws ValidationException
     */
    public function __construct($value)
    {
        $this->value = $value;
        if (!$value instanceof ST_One && !$value instanceof ST_Two) {
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
