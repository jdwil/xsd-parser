<?php
declare(strict_types=1);

namespace JDWil\Xsd\Test\Ooxml\OfficeDocument\SharedTypes;

use JDWil\Xsd\Test\Interfaces\SimpleTypeInterface;
use JDWil\Xsd\Test\Exception\ValidationException;
use JDWil\Xsd\Test\Ooxml\OfficeDocument\SharedTypes\ST_Percentage;
use JDWil\Xsd\Test\Interfaces\HasPatternInterface;

class ST_FixedPercentage implements SimpleTypeInterface, HasPatternInterface
{
    /**
     * @var ST_Percentage
     */
    protected $value;

    /**
     * ST_FixedPercentage constructor
     * @param ST_Percentage $value
     * @throws ValidationException
     */
    public function __construct(ST_Percentage $value)
    {
        $this->value = $value;

        if (!preg_match('/-?((100)|(\d\d?))(\.\d\d?)?%/', $this->value)) {
            throw new ValidationException('value does not match pattern "-?((100)|(\d\d?))(\.\d\d?)?%"');
        }
    }

    /**
     * @return ST_Percentage
     */
    public function getValue(): ST_Percentage
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s', $this->value);
    }
}
