<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

use JDWil\Xsd\Exception\ValidationException;

class Base64Binary extends AbstractStringType
{
    /**
     * Base64Binary constructor.
     * @param string $value
     * @throws ValidationException
     */
    public function __construct(string $value)
    {
        if (!$this->isBase64($value)) {
            $this->throwNotValid();
        }

        $this->value = $value;
    }

    /**
     * @return string
     * @throws ValidationException
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $string
     * @return bool
     */
    private function isBase64(string $string): bool
    {
        return (bool) preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $string);
    }

    /**
     * @throws ValidationException
     */
    private function throwNotValid()
    {
        throw new ValidationException('Value is not base64 encoded');
    }
}
