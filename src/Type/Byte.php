<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

use JDWil\Xsd\Exception\ValidationException;

class Byte extends AbstractIntegerType
{
    /**
     * @return string
     * @throws ValidationException
     */
    public function __toString(): string
    {
        if (!$this->isInRange($this->value)) {
            throw new ValidationException('byte is out of range');
        }
        return sprintf('%d', $this->value);
    }

    /**
     * @param int $value
     * @return mixed
     * @throws ValidationException
     */
    public function setValue(int $value)
    {
        if (!$this->isInRange($value)) {
            throw new ValidationException('byte is out of range');
        }

        $this->value = $value;
    }

    /**
     * @param int $number
     * @return bool
     */
    public function isInRange(int $number)
    {
        return $number >= -128 && $number <= 127;
    }
}