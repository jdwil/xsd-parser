<?php
declare(strict_types=1);

namespace JDWil\Xsd\Translator;

class PlainTextTranslator extends AbstractTranslator
{
    protected function printDomDocument(\DOMDocument $document, string $indent = '')
    {
        $this->stream->write(sprintf("%sDOM Document:\n", $indent));
        $this->stream->write(sprintf("%s  Actual Encoding:       %s\n", $indent, $document->actualEncoding));
        $this->stream->write(sprintf("%s  Document URI:          %s\n", $indent, $document->documentURI));
        $this->stream->write(sprintf("%s  Encoding:              %s\n", $indent, $document->encoding));
        $this->stream->write(sprintf("%s  Format Output:         %b\n", $indent, $document->formatOutput));
        $this->stream->write(sprintf("%s  Preserve White Space:  %b\n", $indent, $document->preserveWhiteSpace));
        $this->stream->write(sprintf("%s  Recover:               %b\n", $indent, $document->recover));
        $this->stream->write(sprintf("%s  Resolve Externals:     %b\n", $indent, $document->resolveExternals));
        $this->stream->write(sprintf("%s  Standalone:            %b\n", $indent, $document->standalone));
        $this->stream->write(sprintf("%s  Strict Error Checking: %b\n", $indent, $document->strictErrorChecking));
        $this->stream->write(sprintf("%s  Substitute Entities:   %b\n", $indent, $document->substituteEntities));
        $this->stream->write(sprintf("%s  Validate on Parse:     %b\n", $indent, $document->validateOnParse));
        $this->stream->write(sprintf("%s  Version:               %s\n", $indent, $document->version));
        $this->stream->write(sprintf("%s  XML Encoding:          %s\n", $indent, $document->xmlEncoding));
        $this->stream->write(sprintf("%s  XML Standalone:        %b\n", $indent, $document->xmlStandalone));
        $this->stream->write(sprintf("%s  XML Version:           %s\n", $indent, $document->xmlVersion));
        $this->stream->write("\n");
    }

    protected function printDomElement(\DOMElement $node, string $indent = '')
    {
        $this->stream->write(sprintf("%sDocument Element:\n", $indent));
        $this->stream->write(sprintf("%s  Tag Name:         %s\n", $indent, $node->tagName));
        $this->stream->write(sprintf("%s  Schema Type Info: %b\n", $indent, $node->schemaTypeInfo));
        $this->printDomNode($node, $indent, false);
        $this->stream->write("\n");
    }

    protected function printDomNode(\DOMNode $node, string $indent = '', bool $printHeader = true)
    {
        if ($printHeader) {
            $this->stream->write(sprintf("%sDOM Node:\n", $indent));
        }

        $this->stream->write(sprintf("%s  Node Name:        %s\n", $indent, $node->nodeName));
        if (!empty(trim($node->nodeValue))) {
            $this->stream->write(sprintf("%s  Node Value:       %s\n", $indent, $node->nodeValue));
        }
        $this->stream->write(sprintf("%s  Node Type:        %s\n", $indent, $this->nodeType($node->nodeType)));
        $this->stream->write(sprintf("%s  Namespace URI:    %s\n", $indent, $node->namespaceURI));
        $this->stream->write(sprintf("%s  Prefix:           %s\n", $indent, $node->prefix));
        $this->stream->write(sprintf("%s  Local Name:       %s\n", $indent, $node->localName));
        $this->stream->write(sprintf("%s  Base URI:         %s\n", $indent, $node->baseURI));
        //$this->stream->write(sprintf("%s  Parent Node:\n", $indent));
        //$this->print($node->parentNode, $indent . '    ');

        if ($node->attributes) {
            $this->stream->write(sprintf("%s  Attributes:\n", $indent));
            foreach ($node->attributes as $attribute) {
                $this->printDomNode($attribute, $indent . '    ');
            }
        }

        if ($node->childNodes) {
            $this->stream->write(sprintf("%s  Children:\n", $indent));
            foreach ($node->childNodes as $childNode) {
                $this->printDomNode($childNode, $indent . '    ');
            }
        }

        $this->stream->write("\n");
    }

    private function print(\DOMNode $node, string $indent)
    {
        if (!$node) {
            $this->stream->write(sprintf("%sNULL\n", $indent));
            return;
        }

        if ($node instanceof Document) {
            $this->printDomDocument($node, $indent);
        } else if ($node instanceof \DOMElement) {
            $this->printDomElement($node, $indent);
        }
    }
}
