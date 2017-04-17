<?php
declare(strict_types=1);

namespace JDWil\Xsd\Translator;

use JDWil\Xsd\Stream\OutputStream;

abstract class AbstractTranslator implements TranslatorInterface
{
    /**
     * @var OutputStream
     */
    protected $stream;

    public function translate(\DOMDocument $document, OutputStream $stream)
    {
        $this->stream = $stream;
        $this->printDomDocument($document);
        $this->printDomElement($document->documentElement);
    }

    abstract protected function printDomDocument(\DOMDocument $document, string $indent = '');

    abstract protected function printDomElement(\DOMElement $node, string $indent = '');

    abstract protected function printDomNode(\DOMNode $node, string $indent = '', bool $printHeader = true);

    protected function nodeType(int $type): string
    {
        switch ($type) {
            case XML_ELEMENT_NODE:
                return 'ELEMENT';
            case XML_ATTRIBUTE_NODE:
                return 'ATTRIBUTE';
            case XML_TEXT_NODE:
                return 'TEXT';
            case XML_CDATA_SECTION_NODE:
                return 'CDATA';
            case XML_ENTITY_REF_NODE:
                return 'ENTITY_REF';
            case XML_ENTITY_NODE:
                return 'ENTITY';
            case XML_PI_NODE:
                return 'PI';
            case XML_COMMENT_NODE:
                return 'COMMENT';
            case XML_DOCUMENT_NODE:
                return 'DOCUMENT';
            case XML_DOCUMENT_TYPE_NODE:
                return 'DOCUMENT_TYPE';
            case XML_DOCUMENT_FRAG_NODE:
                return 'DOCUMENT_FRAG';
            case XML_NOTATION_NODE:
                return 'NOTATION';
            case XML_HTML_DOCUMENT_NODE:
                return 'HTML_DOCUMENT';
            case XML_DTD_NODE:
                return 'DTD';
            case XML_ELEMENT_DECL_NODE:
                return 'ELEMENT_DECL';
            case XML_ATTRIBUTE_DECL_NODE:
                return 'ATTRIBUTE_DECL';
            case XML_ENTITY_DECL_NODE:
                return 'ENTITY_DECL';
            case XML_NAMESPACE_DECL_NODE:
                return 'NAMESPACE_DECL';
        }

        return 'UNKNOWN';
    }
}
