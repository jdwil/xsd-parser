<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;


class NormalizedString extends AbstractStringType
{
    /**
     * StringTypeInterface constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = (string) preg_replace('/([\n\r\t])|(\s{2,})/', ' ', $value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
