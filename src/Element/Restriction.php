<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

use JDWil\Xsd\Facet\Enumeration;
use JDWil\Xsd\Facet\FacetInterface;
use JDWil\Xsd\ValueObject\Enum;

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
        $this->facets = [];
        parent::__construct($id);
    }

    /**
     * @return bool
     */
    public function isEnum(): bool
    {
        foreach ($this->facets as $facet) {
            if ($facet instanceof Enumeration) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Enum|bool
     */
    public function getEnumValues()
    {
        if (!$this->isEnum()) {
            return false;
        }

        $ret = new Enum();
        foreach ($this->getFacets() as $facet) {
            $ret->add($facet->getValue());
        }

        return $ret;
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

    /**
     * @return string
     */
    public function getBase(): string
    {
        return $this->base;
    }
}
