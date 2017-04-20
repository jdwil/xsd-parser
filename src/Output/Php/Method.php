<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

/**
 * Class Method
 * @package JDWil\Xsd\Output\Php
 */
class Method
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $visibility = 'public';

    /**
     * @var bool
     */
    public $static = false;

    /**
     * @var array
     */
    public $arguments = [];

    /**
     * @var string
     */
    public $returns;

    /**
     * @var string
     */
    public $body;

    /**
     * @param string $name
     * @param string $type
     */
    public function addArgument(string $name, string $type)
    {
        $this->arguments[$name] = $type;
    }
}
