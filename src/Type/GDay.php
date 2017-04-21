<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

class GDay
{
    protected $value;

    public function __construct(string $value)
    {
        $this->value = new \DateTime();
    }
}