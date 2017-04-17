<?php
declare(strict_types=1);

namespace JDWil\Xsd\Translator;

use JDWil\Xsd\Stream\OutputStream;

/**
 * Interface TranslatorInterface
 * @package JDWil\Xsd\Translator
 */
interface TranslatorInterface
{
    /**
     * @param \DOMDocument $document
     * @param OutputStream $stream
     * @return mixed
     */
    public function translate(\DOMDocument $document, OutputStream $stream);
}
