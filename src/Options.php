<?php
declare(strict_types=1);

namespace JDWil\Xsd;

class Options
{
    public $debug;

    public $verbose;

    public function __construct()
    {
        $this->debug = false;
        $this->verbose = false;
    }

    public static function forDebugging(): Options
    {
        $ret = new Options();
        $ret->debug = true;
        $ret->verbose = true;

        return $ret;
    }
}
