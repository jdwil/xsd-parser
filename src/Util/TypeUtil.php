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
        return in_array($type, ['bool', 'int', 'float', 'string'], true);
    }

    /**
     * @param $value
     * @return string
     */
    public static function typeSpecifier($value): string
    {
        if (preg_match('/[0-9\.-]+/', $value)) {
            return strpos($value, '.') !== false ? '%f' : '%d';
        } else if ((string) $value === 'true' || (string) $value === 'false') {
            return '%b';
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
        $type = gettype($variable);
        switch ($type) {
            case 'boolean':
                return 'bool';
            case 'integer':
                return 'int';
            default:
                return $type;
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

            case 'short':
            case 'long':
            case 'int':
            case 'integer':
            case 'unsignedLong':
            case 'unsignedInt':
            case 'unsignedShort':
                return 'int';

            case 'decimal':
                return 'float';

            default:
                return null;
        }
    }
}
