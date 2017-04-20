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
     * @var Argument[]
     */
    public $arguments = [];

    /**
     * @var string[]
     */
    public $throws = [];

    /**
     * @var string
     */
    public $returns = false;

    /**
     * @var bool
     */
    public $returnsNull;

    /**
     * @var string
     */
    public $body;

    /**
     * @param Argument $argument
     */
    public function addArgument(Argument $argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * @param string $throws
     */
    public function throws(string $throws)
    {
        $this->throws[] = $throws;
    }
}
