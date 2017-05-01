<?php
declare(strict_types=1);

namespace JDWil\Xsd\Util;

/**
 * Class TypeUtil
 * @package JDWil\Xsd\Util
 */
class TypeUtil
{
    /**
     * @param string $type
     * @return bool
     */
    public static function isPrimitive(string $type): bool
    {
        return in_array($type, ['bool', 'int', 'float', 'double', 'string', '[]'], true);
    }

    /**
     * @param $value
     * @param bool $lookForReserveWords
     * @return string
     */
    public static function typeSpecifier($value, bool $lookForReserveWords = true): string
    {
        if ($lookForReserveWords) {
            switch ($value) {
                case 'bool':
                    return '%s';

                case 'string':
                    return "'%s'";

                case 'float':
                case 'double':
                    return '%f';

                case 'int':
                case 'integer':
                    return '%d';
            }
        }

        if (preg_match('/^\-?\d+\.?\d*$/', (string) $value)) {
            return strpos((string) $value, '.') !== false ? '%f' : '%d';
        } else if ((string) $value === 'true' || (string) $value === 'false') {
            return '%s';
        } else if ((string) $value === '[]') {
            return '%s';
        } else {
            return "'%s'";
        }
    }

    /**
     * @param $variable
     * @return string
     */
    public static function getVarType($variable): string
    {
        if (preg_match('/^\-?\d+\.?\d*$/', (string) $variable)) {
            return strpos((string) $variable, '.') !== false ? 'float' : 'int';
        } else if ((string) $variable === 'true' || (string) $variable === 'false') {
            return 'bool';
        } else if ((string) $variable === '[]') {
            return 'array';
        } else {
            return 'string';
        }
    }

    /**
     * @param string $type
     * @return null|string
     */
    public static function typeToPhpPrimitive(string $type)
    {
        $ns = null;
        if (strpos($type, ':') !== false) {
            list($ns, $type) = explode(':', $type);
        }

        if ($ns !== null && $ns !== 'xsd' && $ns !== 'xs') {
            return null;
        }

        switch ($type) {
            case 'string':
            case 'ID':
            case 'IDREF':
            case 'IDREFS':
                return 'string';

            case 'boolean':
                return 'bool';

            case 'long':
            case 'int':
            case 'integer':
                return 'int';

            case 'decimal':
            case 'float':
                return 'float';

            case 'double':
                return 'double';

            default:
                return null;
        }
    }
}
