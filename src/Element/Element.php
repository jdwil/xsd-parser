<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Element
 * @package JDWil\Xsd\Element
 */
class Element extends IdentifiableElement
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
    protected $type;

    /**
     * @var string
     */
    protected $substitutionGroup;

    /**
     * @var string
     */
    protected $default;

    /**
     * @var string
     */
    protected $fixed;

    /**
     * @var string
     */
    protected $form;

    /**
     * @var string
     */
    protected $maxOccurs;

    /**
     * @var int
     */
    protected $minOccurs;

    /**
     * @var bool
     */
    protected $nillable;

    /**
     * @var bool
     */
    protected $abstract;

    /**
     * @var string
     */
    protected $block;

    /**
     * @var string
     */
    protected $final;

    /**
     * Element constructor.
     * @param string|null $id
     * @param string|null $name
     * @param string|null $ref
     * @param string|null $type
     * @param string|null $substitutionGroup
     * @param string|null $default
     * @param string|null $fixed
     * @param string|null $form
     * @param string $maxOccurs
     * @param int $minOccurs
     * @param bool $nillable
     * @param bool $abstract
     * @param string|null $block
     * @param string|null $final
     */
    public function __construct(
        string $id = null,
        string $name = null,
        string $ref = null,
        string $type = null,
        string $substitutionGroup = null,
        string $default = null,
        string $fixed = null,
        string $form = null,
        string $maxOccurs = '1',
        int $minOccurs = 1,
        bool $nillable = false,
        bool $abstract = false,
        string $block = null,
        string $final = null
    ) {
        $this->name = $name;
        $this->ref = $ref;
        $this->type = $type;
        $this->substitutionGroup = $substitutionGroup;
        $this->default = $default;
        $this->fixed = $fixed;
        $this->form = $form;
        $this->maxOccurs = $maxOccurs;
        $this->minOccurs = $minOccurs;
        $this->nillable = $nillable;
        $this->abstract = $abstract;
        $this->block = $block;
        $this->final = $final;
        parent::__construct($id);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getSubstitutionGroup()
    {
        return $this->substitutionGroup;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getFixed()
    {
        return $this->fixed;
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->form;
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

    /**
     * @return bool
     */
    public function isNillable(): bool
    {
        return $this->nillable;
    }

    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    /**
     * @return string
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @return string
     */
    public function getFinal()
    {
        return $this->final;
    }
}
