<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\Ooxml\Spreadsheetml\Main;

use JDWil\Xsd\Test\Interfaces\SimpleTypeInterface;
use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\Interfaces\HasLengthInterface;

class ST_TDInt implements SimpleTypeInterface, HasLengthInterface
{
    /**
     * @var int
     */
    protected $value;

    /**
     * ST_TDInt constructor
     * @param int $value
     * @throws ValidationException
     */
    public function __construct(int $value)
    {
        $this->value = $value;

        if (4 !== preg_match_all('/\d/', $this->value)) {
            throw new ValidationException('value must contain 4 digits');
        }
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%d', $this->value);
    }
}
