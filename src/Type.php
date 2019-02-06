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
}
