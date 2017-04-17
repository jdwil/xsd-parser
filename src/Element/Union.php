<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Union
 * @package JDWil\Xsd\Element
 */
class Union extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $memberTypes;

    /**
     * Union constructor.
     * @param string|null $id
     * @param string|null $memberTypes
     */
    public function __construct(string $id = null, string $memberTypes = null)
    {
        $this->memberTypes = $memberTypes;
        parent::__construct($id);
    }
}