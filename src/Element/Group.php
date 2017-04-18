<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

use JDWil\Xsd\Exception\ValidationException;

/**
 * Class Group
 * @package JDWil\Xsd\Element
 */
class Group extends IdentifiableElement
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
     * @var string
     */
    protected $maxOccurs;

    /**
     * @var int
     */
    protected $minOccurs;

    /**
     * Group constructor.
     * @param string|null $id
     * @param string|null $name
     * @param string|null $ref
     * @param string $maxOccurs
     * @param int $minOccurs
     * @throws ValidationException
     */
    public function __construct(
        string $id = null,
        string $name = null,
        string $ref = null,
        string $maxOccurs = '1',
        int $minOccurs = 1
    ) {
        if (null !== $name && null !== $ref) {
            throw new ValidationException('name and ref can not both be present.');
        }

        if (!preg_match('/\d+/', $maxOccurs) && $maxOccurs !== 'unbounded') {
            throw new ValidationException('maxOccurs must be an integer or "unbounded".');
        }

        $this->name = $name;
        $this->ref = $ref;
        $this->maxOccurs = $maxOccurs;
        $this->minOccurs = $minOccurs;
        parent::__construct($id);
    }
}
