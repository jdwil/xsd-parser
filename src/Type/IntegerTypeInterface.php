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
     * @param int $value
     * @return mixed
     */
    public function setValue(int $value);

    /**
     * @return int
     */
    public function getValue(): int;
}