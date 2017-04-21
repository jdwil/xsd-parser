<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

/**
 * Interface StringTypeInterface
 * @package JDWil\Xsd\Type
 */
interface StringTypeInterface
{
    /**
     * StringTypeInterface constructor.
     * @param string $value
     */
    public function __construct(string $value);

    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @param string $value
     */
    public function setValue(string $value);

    /**
     * @return string
     */
    public function getValue(): string;
}