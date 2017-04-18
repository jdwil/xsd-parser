<?php
declare(strict_types=1);

namespace JDWil\Xsd;

class Options
{
    public $debug;

    public $verbose;

    public $declareStrictTypes;

    public $docComment;

    public $namespacePrefix;

    public $propertyVisibility;

    public function __construct()
    {
        $this->debug = false;
        $this->verbose = false;
        $this->declareStrictTypes = true;
        $this->docComment = null;
        $this->namespacePrefix = '';
        $this->propertyVisibility = 'private';
    }

    public static function forDebugging(): Options
    {
        $ret = new Options();
        $ret->debug = true;
        $ret->verbose = true;

        return $ret;
    }
}
