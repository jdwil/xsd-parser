<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Restriction
 * @package JDWil\Xsd\Element
 */
class Restriction extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $base;

    /**
     * Restriction constructor.
     * @param string $base
     * @param string|null $id
     */
    public function __construct(string $base, string $id = null)
    {
        $this->base = $base;
        parent::__construct($id);
    }
}