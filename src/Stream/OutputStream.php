<?php
declare(strict_types=1);

namespace JDWil\Xsd\Stream;

class OutputStream implements OutputStreamInterface
{
    /**
     * @var
     */
    private $handle;

    /**
     * OutputStream constructor.
     */
    private function __construct() {}

    /**
     * @param string $target
     * @return OutputStream
     */
    public static function streamedTo(string $target): OutputStream
    {
        $ret = new OutputStream();
        $ret->handle = fopen($target, 'wb');

        return $ret;
    }

    /**
     * @param string $data
     */
    public function write(string $data)
    {
        fwrite($this->handle, $data);
    }

    /**
     * @param string $data
     */
    public function writeLine(string $data)
    {
        $this->write(sprintf("%s\n", $data));
    }
}
