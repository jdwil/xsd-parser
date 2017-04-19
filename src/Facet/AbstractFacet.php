<?php
declare(strict_types=1);

namespace JDWil\Xsd\Facet;

/**
 * Class AbstractFacet
 * @package JDWil\Xsd\Facet
 */
class AbstractFacet implements FacetInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * AbstractFacet constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
