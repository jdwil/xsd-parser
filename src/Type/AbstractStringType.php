<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

abstract class AbstractStringType implements StringTypeInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
