<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

use JDWil\Xsd\Exception\ValidationException;

class AnyURI extends AbstractStringType
{
    /**
     * @return string
     * @throws ValidationException
     */
    public function __toString(): string
    {
        if (!$this->isValidURI($this->value)) {
            throw new ValidationException('value must be a valid URI');
        }

        return $this->value;
    }

    public function setValue(string $value)
    {
        if (!$this->isValidURI($value)) {
            throw new ValidationException('value must be a valid URI');
        }

        $this->value = $value;
    }

    /**
     * @param string $uri
     * @return bool
     */
    private function isValidURI(string $uri): bool
    {
        return filter_var($uri, FILTER_VALIDATE_URL) !== false;
    }
}