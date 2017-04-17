<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Any
 * @package JDWil\Xsd\Element
 */
class Any extends IdentifiableElement
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
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $processContents;

    /**
     * Any constructor.
     * @param string|null $id
     * @param int $maxOccurs
     * @param int $minOccurs
     * @param string $namespace
     * @param string $processContents
     */
    public function __construct(
        string $id = null,
        int $maxOccurs = 1,
        int $minOccurs = 1,
        string $namespace = '##any',
        string $processContents = 'strict'
    ) {
        $this->maxOccurs = $maxOccurs;
        $this->minOccurs = $minOccurs;
        $this->namespace = $namespace;
        $this->processContents = $processContents;
        parent::__construct($id);
    }
}