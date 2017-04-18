<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

use JDWil\Xsd\DOM\Definition;
use JDWil\Xsd\Element\Attribute;
use JDWil\Xsd\Element\ComplexType;
use JDWil\Xsd\Options;

class ClassGenerator
{
    private $options;

    private $definition;

    /**
     * @var ComplexType
     */
    private $type;

    /**
     * @var array
     */
    private $constructorArgs;

    public function __construct(Options $options, Definition $definition)
    {
        $this->options = $options;
        $this->definition = $definition;
        $this->constructorArgs = [];
    }

    public function generate(ComplexType $type)
    {
        $this->type = $type;
        $outputDir = '/Users/jwilliams/dev/jdwil/output';
        $templateDir = __DIR__ . '/templates';
        $template = file_get_contents(sprintf('%s/class.template', $templateDir));

        $template = $this->processDeclarations($template);
        $template = $this->processDocComment($template);
        $template = $this->processNamespace($template);
        $template = $this->processUses($template);
        $template = $this->processClassComment($template);
        $template = $this->processClassModifiers($template);
        $template = $this->processClassName($template);
        $template = $this->processClassType($template);
        $template = $this->processClassExtends($template);
        $template = $this->processClassImplements($template);
        $template = $this->processClassProperties($template);
        $template = $this->processClassConstructor($template);
        $template = $this->processClassMethods($template);

        file_put_contents(sprintf('%s/%s.php', $outputDir, $type->getName()), $template);
    }

    private function processDeclarations(string $template): string
    {
        $declarations = $this->options->declareStrictTypes ? "declare(strict_types=1);\n" : "\n";
        return str_replace('__DECLARE__', $declarations, $template);
    }

    private function processDocComment(string $template): string
    {
        $docComment = $this->options->docComment ?? "\n";
        return str_replace('__DOC_COMMENT__', $docComment, $template);
    }

    private function processNamespace(string $template): string
    {
        $schema = $this->type->getSchema();
        if ($xmlns = $schema->getXmlns()) {
            $pieces = explode('/', $schema->getXmlns());
            $namespace = ucwords(array_pop($pieces));
        } else {
            $namespace = '';
        }

        $separator = '';
        if (strlen($this->options->namespacePrefix) && substr($this->options->namespacePrefix, -1) !== '\\') {
            $separator = '\\';
        }

        if (strlen($this->options->namespacePrefix) || strlen($namespace)) {
            $namespace = sprintf("namespace %s%s%s;\n\n", $this->options->namespacePrefix, $separator, $namespace);
            if (substr($namespace, -1) === '\\') {
                $namespace = substr($namespace, 0, -1);
            }
            return str_replace('__NAMESPACE__', $namespace, $template);
        } else {
            return str_replace('__NAMESPACE__', '', $template);
        }
    }

    private function processUses(string $template): string
    {
        return str_replace('__USES__', '', $template);
    }

    private function processClassComment(string $template): string
    {
        $name = $this->getClassName();
        $comment = <<<__COMMENT__
/**
 * class $name
 */
__COMMENT__;
        return str_replace('__CLASS_COMMENT__', $comment, $template);
    }

    private function processClassModifiers(string $template): string
    {
        return str_replace('__CLASS_MODIFIERS__', '', $template);
    }

    private function processClassName(string $template): string
    {
        return str_replace('__CLASS_NAME__', $this->getClassName(), $template);
    }

    private function processClassType(string $template): string
    {
        return str_replace('__CLASS_TYPE__', 'class', $template);
    }

    private function processClassExtends(string $template): string
    {
        return str_replace('__EXTENDS__', '', $template);
    }

    private function processClassImplements(string $template): string
    {
        return str_replace('__IMPLEMENTS__', '', $template);
    }

    private function processClassProperties(string $template): string
    {
        $string = '';
        foreach ($this->type->getChildren() as $child) {
            if ($child instanceof Attribute) {
                if ($ref = $child->getRef()) {
                    // @todo get referenced attribute
                } else {
                    $type = $child->getType();
                    $name = $child->getName();
                    $doc = <<<__COMMENT__
    /**
     * @var $type $name
     */

__COMMENT__;
                    $string .= $doc;
                    $string .= sprintf("    %s \$%s;\n\n", $this->options->propertyVisibility, $name);
                    $this->constructorArgs[] = [
                        'name' => $name,
                        'type' => $type,
                        'default' => $child->getDefault(),
                        'use' => $child->getUse(),
                        'fixed' => $child->getFixed()
                    ];
                }
            }
        }

        return str_replace('__PROPERTIES__', $string, $template);
    }

    private function processClassConstructor(string $template): string
    {
        return str_replace('__CONSTRUCTOR__', '', $template);
    }

    private function processClassMethods(string $template): string
    {
        return str_replace('__METHODS__', '', $template);
    }

    private function getClassName(): string
    {
        return $this->type->getName();
    }
}