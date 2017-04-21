<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

use JDWil\Xsd\Exception\ValidationException;

class Base64Binary extends AbstractStringType
{
    /**
     * @return string
     * @throws ValidationException
     */
    public function __toString(): string
    {
        if (!$this->isBase64($this->value)) {
            throw new ValidationException('Value is not base64 encoded');
        }

        return $this->value;
    }

    /**
     * @param string $value
     * @throws ValidationException
     */
    public function setValue(string $value)
    {
        if (!$this->isBase64($value)) {
            throw new ValidationException('Value is not base64 encoded');
        }

        $this->value = $value;
    }

    /**
     * @param string $string
     * @return bool
     */
    private function isBase64(string $string): bool
    {
        return (bool) preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $string);
    }
}