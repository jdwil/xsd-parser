<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Exception\ValidationException;

class ST_Min
{
    /**
     * @var int
     */
    protected $value;

    /**
     * ST_Min constructor
     * @param int $value
     * @throws ValidationException
     */
    public function __construct(int $value)
    {
        $this->value = $value;

        if ($this->value <= 0) {
            throw new ValidationException('value out of bounds');
        }
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
