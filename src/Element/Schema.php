<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Schema
 * @package JDWil\Xsd\Element
 */
class Schema extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $attributeFormDefault;

    /**
     * @var string
     */
    protected $elementFormDefault;

    /**
     * @var string
     */
    protected $blockDefault;

    /**
     * @var string
     */
    protected $finalDefault;

    /**
     * @var string
     */
    protected $targetNamespace;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $xmlns;

    /**
     * @var array
     */
    protected $namespaces;

    /**
     * Schema constructor.
     * @param string|null $id
     * @param string|null $attributeFormDefault
     * @param string|null $elementFormDefault
     * @param string|null $blockDefault
     * @param string|null $finalDefault
     * @param string|null $targetNamespace
     * @param string|null $version
     * @param string|null $xmlns
     */
    public function __construct(
        string $id = null,
        string $attributeFormDefault = null,
        string $elementFormDefault = null,
        string $blockDefault = null,
        string $finalDefault = null,
        string $targetNamespace = null,
        string $version = null,
        string $xmlns = null
    ) {
        $this->attributeFormDefault = $attributeFormDefault;
        $this->elementFormDefault = $elementFormDefault;
        $this->blockDefault = $blockDefault;
        $this->finalDefault = $finalDefault;
        $this->targetNamespace = $targetNamespace;
        $this->version = $version;
        $this->xmlns = $xmlns;

        $this->attributeElements = [];
        $this->namespaces = [];

        parent::__construct($id);
    }

    /**
     * @return string
     */
    public function getAttributeFormDefault(): string
    {
        return $this->attributeFormDefault;
    }

    /**
     * @return string
     */
    public function getElementFormDefault()
    {
        return $this->elementFormDefault;
    }

    /**
     * @return string
     */
    public function getBlockDefault()
    {
        return $this->blockDefault;
    }

    /**
     * @return string
     */
    public function getFinalDefault()
    {
        return $this->finalDefault;
    }

    /**
     * @return string
     */
    public function getTargetNamespace()
    {
        return $this->targetNamespace;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getXmlns()
    {
        return $this->xmlns;
    }

    /**
     * @param string $xmlns
     */
    public function setXmlns(string $xmlns)
    {
        $this->xmlns = $xmlns;
    }

    /**
     * @param string $alias
     * @param string $value
     */
    public function addNamespace(string $alias, string $value)
    {
        $this->namespaces[$alias] = $value;
    }

    /**
     * @return array
     */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    /**
     * @param string $alias
     * @return string
     */
    public function findNamespaceByAlias(string $alias): string
    {
        return $this->namespaces[$alias];
    }
}
