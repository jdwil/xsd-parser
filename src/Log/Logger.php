<?php
declare(strict_types=1);

namespace JDWil\Xsd\Log;

/**
 * Class Logger
 * @package JDWil\Xsd\Log
 */
class Logger implements LoggerInterface
{
    /**
     * @param string $message
     */
    public function debug(string $message)
    {
        printf("DEBUG: %s\n", $message);
    }
}
