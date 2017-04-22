<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Extension
 * @package JDWil\Xsd\Element
 */
class Extension extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $base;

    /**
     * Extension constructor.
     * @param string $base
     * @param string|null $id
     */
    public function __construct(string $base, string $id = null)
    {
        $this->base = $base;
        parent::__construct($id);
    }

    /**
     * @return string
     */
    public function getBase(): string
    {
        return $this->base;
    }
}
