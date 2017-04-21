<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

class Date
{
    /**
     * @var \DateTime
     */
    protected $value;

    /**
     * Date constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = new \DateTime($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value->format('Y-m-dP');
    }

    /**
     * @return \DateTime
     */
    public function getValue(): \DateTime
    {
        return $this->value;
    }

    /**
     * @param \DateTime $value
     */
    public function setValue(\DateTime $value)
    {
        $this->value = $value;
    }
}