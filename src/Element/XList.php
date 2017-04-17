<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;


/**
 * Class XList
 * @package JDWil\Xsd\Element
 */
class XList extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $itemType;

    /**
     * XList constructor.
     * @param string|null $id
     * @param string|null $itemType
     */
    public function __construct(string $id = null, string $itemType = null)
    {
        $this->itemType = $itemType;
        parent::__construct($id);
    }
}