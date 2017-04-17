<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class ComplexContent
 * @package JDWil\Xsd\Element
 */
class ComplexContent extends IdentifiableElement
{
    /**
     * @var bool
     */
    protected $mixed;

    /**
     * ComplexContent constructor.
     * @param string|null $id
     * @param bool $mixed
     */
    public function __construct(string $id = null, bool $mixed = false)
    {
        $this->mixed = $mixed;
        parent::__construct($id);
    }
}