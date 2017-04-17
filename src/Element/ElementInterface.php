<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Interface ElementInterface
 * @package JDWil\Xsd\Element
 */
interface ElementInterface
{
    /**
     * @return array
     */
    public function getAttributes(): array;

    /**
     * @param string $key
     * @param string $value
     */
    public function addAttribute(string $key, string $value);
}