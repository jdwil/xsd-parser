<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

use JDWil\Xsd\Exception\ValidationException;

/**
 * Class Choice
 * @package JDWil\Xsd\Element
 */
class Choice extends IdentifiableElement
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
     * Choice constructor.
     * @param null $id
     * @param string $maxOccurs
     * @param int $minOccurs
     * @throws ValidationException
     */
    public function __construct($id = null, string $maxOccurs = '1', int $minOccurs = 1)
    {
        if (!preg_match('/\d+/', $maxOccurs) && $maxOccurs !== 'unbounded') {
            throw new ValidationException('maxOccurs must be a number or "unbounded"');
        }

        $this->maxOccurs = $maxOccurs;
        $this->minOccurs = $minOccurs;
        parent::__construct($id);
    }

    /**
     * @return string
     */
    public function getMaxOccurs(): string
    {
        return $this->maxOccurs;
    }

    /**
     * @return int
     */
    public function getMinOccurs(): int
    {
        return $this->minOccurs;
    }
}
