<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\Xsd\Token;

class ST_Enum
{
    const VALUE_ONE = 'one';

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

        if (self::VALUE_ONE !== $this->value) {
            throw new ValidationException('value must be one of self::VALUE_ONE');
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
