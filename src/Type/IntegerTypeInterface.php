<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

interface IntegerTypeInterface
{
    /**
     * IntegerTypeInterface constructor.
     * @param int $value
     */
    public function __construct(int $value);

    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @return int
     */
    public function getValue(): int;
}
