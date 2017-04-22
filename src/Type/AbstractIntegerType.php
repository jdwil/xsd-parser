<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

abstract class AbstractIntegerType implements IntegerTypeInterface
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
