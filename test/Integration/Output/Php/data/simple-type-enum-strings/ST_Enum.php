<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Exception\ValidationException;

class ST_Enum
{
    const VALUE_ONE = 'one';
    const VALUE_TWO = 'two';
    const VALUE_THREE = 'three';

    /**
     * @var string
     */
    protected $value;

    /**
     * ST_Enum constructor
     * @param string $value
     * @throws ValidationException
     */
    public function __construct(string $value)
    {
        $this->value = $value;

        if (!in_array($this->value, [self::VALUE_ONE, self::VALUE_TWO, self::VALUE_THREE], true)) {
            throw new ValidationException('value must be one of self::VALUE_ONE, self::VALUE_TWO, self::VALUE_THREE');
        }
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
