<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class IdentifiableElement
 * @package JDWil\Xsd\Element
 */
class IdentifiableElement extends AbstractElement
{
    /**
     * @var string
     */
    protected $id;

    /**
     * AbstractElement constructor.
     * @param string|null $id
     */
    public function __construct(string $id = null)
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
    }
}