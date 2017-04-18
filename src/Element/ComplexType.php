<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class ComplexType
 * @package JDWil\Xsd\Element
 */
class ComplexType extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $abstract;

    /**
     * @var bool
     */
    protected $mixed;

    /**
     * @var string
     */
    protected $block;

    /**
     * @var string
     */
    protected $final;

    /**
     * ComplexType constructor.
     * @param string|null $id
     * @param string|null $name
     * @param bool $abstract
     * @param bool $mixed
     * @param string|null $block
     * @param string|null $final
     */
    public function __construct(
        string $id = null,
        string $name = null,
        bool $abstract = false,
        bool $mixed = false,
        string $block = null,
        string $final = null
    ) {
        $this->name = $name;
        $this->abstract = $abstract;
        $this->mixed = $mixed;
        $this->block = $block;
        $this->final = $final;
        parent::__construct($id);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    /**
     * @return bool
     */
    public function isMixed(): bool
    {
        return $this->mixed;
    }

    /**
     * @return string
     */
    public function getBlock(): string
    {
        return $this->block;
    }

    /**
     * @return string
     */
    public function getFinal(): string
    {
        return $this->final;
    }
}
