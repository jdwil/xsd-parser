<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;


class HexBinary extends AbstractStringType
{
    /**
     * StringTypeInterface constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
