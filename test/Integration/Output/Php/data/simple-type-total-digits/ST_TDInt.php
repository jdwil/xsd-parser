<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\SimpleType;

use JDWil\Xsd\Test\Exception\ValidationException;

class ST_TDInt
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
}
