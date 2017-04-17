<?php
declare(strict_types=1);

namespace JDWil\Xsd\Log;

/**
 * Interface LoggerInterface
 * @package JDWil\Xsd\Log
 */
interface LoggerInterface
{
    /**
     * @param string $message
     * @return mixed
     */
    public function debug(string $message);
}
