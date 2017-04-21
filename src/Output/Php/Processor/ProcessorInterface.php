<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Processor;

use JDWil\Xsd\Output\Php\ClassBuilder;

/**
 * Interface ProcessorInterface
 * @package JDWil\Xsd\Output\Php\Processor
 */
interface ProcessorInterface
{
    public function buildClass();
}
