<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class AnyAttribute
 * @package JDWil\Xsd\Element
 */
class AnyAttribute extends IdentifiableElement
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $processContents;

    /**
     * AnyAttribute constructor.
     * @param null $id
     * @param string $namespace
     * @param string $processContents
     */
    public function __construct($id = null, string $namespace = '##any', string $processContents = 'strict')
    {
        $this->namespace = $namespace;
        $this->processContents = $processContents;
        parent::__construct($id);
    }
}