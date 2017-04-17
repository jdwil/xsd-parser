<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

use JDWil\Xsd\Exception\ValidationException;

/**
 * Class AttributeGroup
 * @package JDWil\Xsd\Element
 */
class AttributeGroup extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $ref;

    /**
     * AttributeGroup constructor.
     * @param null $id
     * @param string|null $name
     * @param string|null $ref
     * @throws ValidationException
     */
    public function __construct($id = null, string $name = null, string $ref = null)
    {
        if (null !== $name && null !== $ref) {
            throw new ValidationException('name and ref can not both be present.');
        }

        $this->name = $name;
        $this->ref = $ref;
        parent::__construct($id);
    }
}