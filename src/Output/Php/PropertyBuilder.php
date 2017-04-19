<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;


/**
 * Class PropertyBuilder
 * @package JDWil\Xsd\Output\Php
 */
class PropertyBuilder
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $default;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var bool
     */
    private $fixed;

    /**
     * @var array
     */
    private $enumerations;

    /**
     * PropertyBuilder constructor.
     */
    public function __construct()
    {
        $this->required = false;
        $this->fixed = false;
        $this->enumerations = [];
    }

    /**
     * @return \stdClass
     */
    public function getProperty(): \stdClass
    {
        $ret = new \stdClass();
        $ret->name = $this->name;
        $ret->type = $this->type;
        $ret->default = $this->default;
        $ret->required = $this->required;
        $ret->fixed = $this->fixed;
        $ret->enumerations = $this->enumerations;

        return $ret;
    }

    /**
     * @param string $name
     * @return PropertyBuilder
     */
    public function setName(string $name): PropertyBuilder
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $type
     * @return PropertyBuilder
     */
    public function setType(string $type): PropertyBuilder
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param mixed $default
     * @return PropertyBuilder
     */
    public function setDefault($default): PropertyBuilder
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @param bool $required
     * @return PropertyBuilder
     */
    public function setRequired(bool $required): PropertyBuilder
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @param bool $fixed
     * @return PropertyBuilder
     */
    public function setFixed(bool $fixed): PropertyBuilder
    {
        $this->fixed = $fixed;
        return $this;
    }

    /**
     * @param string $value
     * @return PropertyBuilder
     */
    public function addEnumeration(string $value): PropertyBuilder
    {
        $this->enumerations[] = $value;
        return $this;
    }
}
