<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

class DateTime extends Date
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value->format('Y-m-d\TH:i:sP');
    }
}