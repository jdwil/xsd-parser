<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

use JDWil\Xsd\Exception\ValidationException;

class Byte extends AbstractIntegerType
{
    /**
     * Byte constructor.
     * @param int $value
     * @throws ValidationException
     */
    public function __construct(int $value)
    {
        if (!$this->isInRange($value)) {
            $this->throwNotValid();
        }

        $this->value = $value;
    }

    /**
     * @return string
     * @throws ValidationException
     */
    public function __toString(): string
    {
        return sprintf('%d', $this->value);
    }

    /**
     * @param int $number
     * @return bool
     */
    private function isInRange(int $number)
    {
        return $number >= -128 && $number <= 127;
    }

    /**
     * @throws ValidationException
     */
    private function throwNotValid()
    {
        throw new ValidationException('byte is out of range');
    }
}
