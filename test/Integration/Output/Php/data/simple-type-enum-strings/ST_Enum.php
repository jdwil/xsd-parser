<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\Xsd\Token;

class ST_Enum
{
    const VALUE_ONE = 'one';
    const VALUE_TWO = 'two';
    const VALUE_THREE = 'three';

    /**
     * @var Token
     */
    protected $value;

    /**
     * ST_Enum constructor
     * @param Token $value
     * @throws ValidationException
     */
    public function __construct(Token $value)
    {
        $this->value = $value;

        if (!in_array($this->value, [self::VALUE_ONE, self::VALUE_TWO, self::VALUE_THREE], true)) {
            throw new ValidationException('value must be one of self::VALUE_ONE, self::VALUE_TWO, self::VALUE_THREE');
        }
    }

    /**
     * @return Token
     */
    public function getValue(): Token
    {
        return $this->value;
    }
}
