<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

use JDWil\Xsd\Exception\ValidationException;

/**
 * Class Attribute
 * @package JDWil\Xsd\Element
 */
class Attribute extends IdentifiableElement
{
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
    protected $use;

    /**
     * Attribute constructor.
     * @param string|null $id
     * @param string|null $default
     * @param string|null $fixed
     * @param string|null $form
     * @param string|null $name
     * @param string|null $ref
     * @param string|null $type
     * @param string $use
     * @throws ValidationException
     */
    public function __construct(
        string $id = null,
        string $default = null,
        string $fixed = null,
        string $form = null, // Default is the attributeFormDefault attribute of the parent element
        string $name = null,
        string $ref = null,
        string $type = null,
        string $use = 'optional'
    ) {
        if (null !== $default && null !== $fixed) {
            throw new ValidationException('default and null can not both be present.');
        }

        if (null !== $name && null !== $ref) {
            throw new ValidationException('name and ref can not both be present.');
        }

        $this->default = $default;
        $this->fixed = $fixed;
        $this->form = $form;
        $this->name = $name;
        $this->ref = $ref;
        $this->type = $type;
        $this->use = $use;

        parent::__construct($id);
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
    public function getUse()
    {
        return $this->use;
    }
}
