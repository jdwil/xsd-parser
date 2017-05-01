<?php
declare(strict_types=1);

namespace JDWil\Xsd\Util;

use JDWil\Xsd\Options;

/**
 * Class NamespaceUtil
 * @package JDWil\Xsd\Util
 */
class NamespaceUtil
{
    /**
     * @param Options $options
     * @param string $classNs
     * @param string|null $className
     * @return string
     */
    public static function classNamespace(Options $options, string $classNs, string $className = null): string
    {
        if ($className) {
            return sprintf('%s\\%s\\%s', $options->namespacePrefix, $classNs, $className);
        } else {
            return sprintf('%s\\%s', $options->namespacePrefix, $classNs);
        }
    }

    /**
     * @param Options $options
     * @param string $namespace
     * @return string
     */
    public static function outputDirectory(Options $options, string $namespace): string
    {
        return sprintf('%s/%s', $options->outputDirectory, $namespace);
    }
}
