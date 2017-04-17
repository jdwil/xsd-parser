<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

use JDWil\Xsd\Exception\ValidationException;

/**
 * Class All
 * @package JDWil\Xsd\Element
 */
class All extends IdentifiableElement
{
    /**
     * @var int
     */
    protected $maxOccurs;

    /**
     * @var int
     */
    protected $minOccurs;

    /**
     * All constructor.
     * @param null $id
     * @param int $minOccurs
     * @throws ValidationException
     */
    public function __construct($id = null, $minOccurs = 1)
    {
        if (!in_array($minOccurs, [0, 1])) {
            throw new ValidationException('$minOccurs can only be 0 or 1');
        }

        $this->maxOccurs = 1;
        $this->minOccurs = $minOccurs;
        parent::__construct($id);
    }
}