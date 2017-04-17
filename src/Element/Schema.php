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
    protected $xmins;

    /**
     * Schema constructor.
     * @param string|null $id
     * @param string|null $attributeFormDefault
     * @param string|null $elementFormDefault
     * @param string|null $blockDefault
     * @param string|null $finalDefault
     * @param string|null $targetNamespace
     * @param string|null $version
     * @param string|null $xmins
     */
    public function __construct(
        string $id = null,
        string $attributeFormDefault = null,
        string $elementFormDefault = null,
        string $blockDefault = null,
        string $finalDefault = null,
        string $targetNamespace = null,
        string $version = null,
        string $xmins = null
    ) {
        $this->attributeFormDefault = $attributeFormDefault;
        $this->elementFormDefault = $elementFormDefault;
        $this->blockDefault = $blockDefault;
        $this->finalDefault = $finalDefault;
        $this->targetNamespace = $targetNamespace;
        $this->version = $version;
        $this->xmins = $xmins;
        parent::__construct($id);
    }
}