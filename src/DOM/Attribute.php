<?php
declare(strict_types=1);

namespace JDWil\Xsd\DOM;

/**
 * Class Attribute
 * @package JDWil\Xsd\DOM
 */
class Attribute
{
    const OPTIONAL = 'optional';
    const REQUIRED = 'required';
    const PROHIBITED = 'prohibited';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $use;

    /**
     * Attribute constructor.
     * @param string $name
     * @param string $type
     * @param string $use
     */
    public function __construct(string $name, string $type, string $use = self::OPTIONAL)
    {
        $this->name = $name;
        $this->type = $type;
        $this->use = $use;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUse(): string
    {
        return $this->use;
    }
}
