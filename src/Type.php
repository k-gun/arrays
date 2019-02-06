<?php
declare(strict_types=1);

namespace arrays;

use arrays\StaticClass;

/**
 * @package arrays
 * @object  arrays\Type
 * @author  Kerem Güneş <k-gun@mail.com>
 */
final /* static */ class Type extends StaticClass
{
    /**
     * Types.
     * const int
     */
    public const ANY = 'Any',
                 MAP = 'Map',
                    INT_MAP = 'IntMap', FLOAT_MAP = 'FloatMap',
                    STRING_MAP = 'StringMap', BOOL_MAP = 'BoolMap',
                 SET = 'Set',
                    INT_SET = 'IntSet', FLOAT_SET = 'FloatSet',
                    STRING_SET = 'StringSet', BOOL_SET = 'BoolSet',
                 TUPLE = 'Tuple';

    public static function get($input): string
    {
        return strtr(gettype($input), [
            'NULL'    => 'null',
            'integer' => 'int',
            'double'  => 'float',
            'boolean' => 'bool'
        ]);
    }

    public static function isDigit($input): bool
    {
        return is_int($input) ? true : (
            is_string($input) ? ctype_digit($input) : false
        );
    }

    public static function validateMapKey($key, string &$error = null): bool
    {
        if (!is_string($key)) {
            $error = 'string';
        } elseif (self::isDigit($key)) {
            $error = 'string|digit';
        }
        return $error == null;
    }

    public static function validateSetKey($key, string &$error = null): bool
    {
        if (!is_int($key)) {
            $error = 'string';
        }
        return $error == null;
    }
}
