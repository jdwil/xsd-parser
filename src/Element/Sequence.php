<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

use JDWil\Xsd\Exception\ValidationException;

/**
 * Class Sequence
 * @package JDWil\Xsd\Element
 */
class Sequence extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $maxOccurs;

    /**
     * @var int
     */
    protected $minOccurs;

    /**
     * Sequence constructor.
     * @param string|null $id
     * @param string $maxOccurs
     * @param int $minOccurs
     * @throws ValidationException
     */
    public function __construct(string $id = null, string $maxOccurs = '1', int $minOccurs = 1)
    {
        if (!preg_match('/\d+/', $maxOccurs) && $maxOccurs !== 'unbounded') {
            throw new ValidationException('maxOccurs must be an integer or "unbounded".');
        }

        $this->maxOccurs = $maxOccurs;
        $this->minOccurs = $minOccurs;
        parent::__construct($id);
    }
}