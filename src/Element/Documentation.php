<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class Documentation
 * @package JDWil\Xsd\Element
 */
class Documentation extends AbstractElement
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $lang;

    /**
     * Documentation constructor.
     * @param string|null $source
     * @param string|null $lang
     */
    public function __construct(string $source = null, string $lang = null)
    {
        $this->source = $source;
        $this->lang = $lang;
    }
}