<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

use JDWil\Xsd\Exception\ValidationException;

class GMonthDay
{
    /**
     * @var string
     */
    protected $value;

    /**
     * GMonth constructor.
     * @param string $value
     * @throws ValidationException
     */
    public function __construct(string $value)
    {
        $this->value = (string) str_replace(' ', '', $value);
        if (!$this->isValidGMonthDay($this->value)) {
            $this->throwNotValid();
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isValidGMonthDay(string $value)
    {
        return (bool) preg_match('/\-\-\d{1,2}\-\d{2}Z?[\+-]?(\d{2}:\d{2})?/', $value);
    }

    /**
     * @throws ValidationException
     */
    private function throwNotValid()
    {
        throw new ValidationException('value must be in format "--mm-dd"');
    }
}
