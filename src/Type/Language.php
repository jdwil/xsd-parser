<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

use JDWil\Xsd\Exception\ValidationException;

class Language extends AbstractStringType
{
    /**
     * StringTypeInterface constructor.
     * @param string $value
     * @throws ValidationException
     */
    public function __construct(string $value)
    {
        if (!$this->isValidLanguage($value)) {
            $this->throwNotValid();
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isValidLanguage(string $value)
    {
        return (bool) preg_match('/([a-zA-Z]{2}|[iI]-[a-zA-Z]+|[xX]-[a-zA-Z]{1,8})(-[a-zA-Z]{1,8})*/', $value);
    }

    /**
     * @throws ValidationException
     */
    private function throwNotValid()
    {
        throw new ValidationException('value is not a valid language');
    }
}
