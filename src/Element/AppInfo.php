<?php
declare(strict_types=1);

namespace JDWil\Xsd\Element;

/**
 * Class AppInfo
 * @package JDWil\Xsd\Element
 */
class AppInfo extends AbstractElement
{
    /**
     * @var null|string
     */
    protected $source;

    /**
     * AppInfo constructor.
     * @param string|null $source
     */
    public function __construct(string $source = null)
    {
        $this->source = $source;
    }
}