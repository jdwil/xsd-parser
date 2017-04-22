<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;


use JDWil\Xsd\Exception\ValidationException;

class Short extends AbstractIntegerType
{
    /**
     * IntegerTypeInterface constructor.
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
     */
    public function __toString(): string
    {
        return sprintf('%d', $this->value);
    }

    /**
     * @throws ValidationException
     */
    private function throwNotValid()
    {
        throw new ValidationException('value is out of range');
    }

    /**
     * @param int $value
     * @return bool
     */
    private function isInRange(int $value): bool
    {
        return $value >= -32768 && $value <= 32767;
    }
}
