<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Exception\ValidationException;

class ST_Min
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

        if ($this->value > 0.100000) {
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
}
