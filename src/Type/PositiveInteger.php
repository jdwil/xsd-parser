<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;


use JDWil\Xsd\Exception\ValidationException;

class PositiveInteger
{
    /**
     * IntegerTypeInterface constructor.
     * @param int $value
     * @throws ValidationException
     */
    public function __construct(int $value)
    {
        if (!$this->isPositiveInteger($value)) {
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
        throw new ValidationException('value must be positive');
    }

    /**
     * @param int $value
     * @return bool
     */
    private function isPositiveInteger(int $value): bool
    {
        return $value > 0;
    }
}
