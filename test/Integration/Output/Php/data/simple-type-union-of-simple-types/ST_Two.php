<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\Xsd\Token;

class ST_Two
{
    /**
     * @var Token
     */
    protected $value;

    /**
     * ST_Two constructor
     * @param Token $value
     */
    public function __construct(Token $value)
    {
        $this->value = $value;
    }

    /**
     * @return Token
     */
    public function getValue(): Token
    {
        return $this->value;
    }
}