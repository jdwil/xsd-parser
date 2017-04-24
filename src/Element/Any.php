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

    /**
     * @return int
     */
    public function getMaxOccurs(): int
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

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getProcessContents(): string
    {
        return $this->processContents;
    }
}
