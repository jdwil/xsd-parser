<?php
declare(strict_types=1);

namespace JDWil\Xsd\Type;

use JDWil\Xsd\Exception\ValidationException;

class AnyURI extends AbstractStringType
{
    /**
     * AnyURI constructor.
     * @param string $value
     * @throws ValidationException
     */
    public function __construct(string $value)
    {
        if (!$this->isValidURI($value)) {
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
     * @param string $uri
     * @return bool
     */
    private function isValidURI(string $uri): bool
    {
        return filter_var($uri, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * @throws ValidationException
     */
    private function throwNotValid()
    {
        throw new ValidationException('value must be a valid URI');
    }
}
