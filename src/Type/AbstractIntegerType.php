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
     * AbstractIntegerType constructor.
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}