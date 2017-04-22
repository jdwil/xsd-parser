<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

class Duration
{
    /**
     * @var \DateInterval
     */
    protected $value;

    /**
     * Duration constructor.
     * @param string $value
     * @throws \Exception
     */
    public function __construct(string $value)
    {
        $this->value = new \DateInterval($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value->format('P%yY%mM%dDT%hH%iM%sS');
    }

    /**
     * @return \DateInterval
     */
    public function getValue(): \DateInterval
    {
        return $this->value;
    }
}
