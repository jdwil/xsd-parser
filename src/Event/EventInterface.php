<?php
declare(strict_types=1);

namespace JDWil\Xsd\Event;

use JDWil\Xsd\DOM\Definition;

/**
 * Interface EventInterface
 * @package JDWil\Xsd\Event
 */
interface EventInterface
{
    /**
     * @return \DOMElement
     */
    public function getNode(): \DOMElement;

    /**
     * @return Definition
     */
    public function getDefinition(): Definition;

    /**
     * @return void
     */
    public function stopPropagation();

    /**
     * @return bool
     */
    public function propagationStopped(): bool;
}
