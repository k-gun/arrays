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

    public static function get($input, string $otherType = null): string
    {
        if ($otherType != null) {
            if ($otherType == 'digit' && is_digit($input)) return 'digit';
            if ($otherType == 'numeric' && is_numeric($input)) return 'numeric';
        }

        return strtr(gettype($input), [
            'NULL'    => 'null',
            'integer' => 'int',
            'double'  => 'float',
            'boolean' => 'bool'
        ]);
    }
}
