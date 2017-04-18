<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

use JDWil\Xsd\Facet\FacetInterface;

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
     * @var FacetInterface[]
     */
    protected $facets;

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

    /**
     * @param FacetInterface $facet
     */
    public function addFacet(FacetInterface $facet)
    {
        $this->facets[] = $facet;
    }

    /**
     * @return array
     */
    public function getFacets(): array
    {
        return $this->facets;
    }
}
