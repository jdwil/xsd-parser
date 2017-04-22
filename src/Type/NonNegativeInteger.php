<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;


use JDWil\Xsd\Exception\ValidationException;

class NonNegativeInteger extends AbstractIntegerType
{
    /**
     * IntegerTypeInterface constructor.
     * @param int $value
     * @throws ValidationException
     */
    public function __construct(int $value)
    {
        if (!$this->isNonNegative($value)) {
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
        throw new ValidationException('value must be >= 0');
    }

    /**
     * @param int $value
     * @return bool
     */
    private function isNonNegative(int $value)
    {
        return $value >= 0;
    }
}
