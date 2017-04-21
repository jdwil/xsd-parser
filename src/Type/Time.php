<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

class Time extends Date
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value->format('H:i:sP');
    }
}