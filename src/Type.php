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

    public static final function validateItems(array $items, string $itemsType, string &$error = null): bool
    {
        static $mapMessage = '%ss accept associative arrays with string keys,'.
            ' %s key given (offset: %s, key: %s)',
               $setMessage = '%ss accept non-associative arrays with unsigned-int keys,'.
            ' %s key given (offset: %s, key: %s)';

        $offset = 0;
        switch ($itemsType) {
            // maps
            case self::MAP:
                foreach ($items as $key => $value) {
                    if (!is_string($key)) {
                        $error = sprintf($mapMessage, self::MAP, self::get($key), $offset, $key);
                            return false; }
                    $offset++;
                } break;
            // others
            default:
                $isPrimitiveType = in_array($itemsType, ['int', 'float', 'string', 'bool']);
                foreach ($items as $key => $value) {
                    if ($isPrimitiveType) {
                        if ($itemsType == 'int' && !is_int($value)) {
                            return sprintf('Each item must be type of int, %s given (offset: %s)',
                                self::get($value), $offset);
                        } elseif ($itemsType == 'float' && !is_float($value)) {
                            return sprintf('Each item must be type of float, %s given (offset: %s)',
                                self::get($value), $offset);
                        } elseif ($itemsType == 'string' && !is_string($value)) {
                            return sprintf('Each item must be type of string, %s given (offset: %s)',
                                self::get($value), $offset);
                        } elseif ($itemsType == 'bool' && !is_bool($value)) {
                            return sprintf('Each item must be type of bool, %s given (offset: %s)',
                                self::get($value), $offset);
                        }
                    } elseif ($itemsType == 'array' && !is_array($value)) {
                        return sprintf('Each item must be type of array, %s given (offset: %s)',
                            self::get($value), $offset);
                    } elseif ($itemsType == 'object' && !is_object($value)) {
                        return sprintf('Each item must be type of object, %s given (offset: %s)',
                            self::get($value), $offset);
                    } else {
                        // object type check
                        if (!is_a($value, $itemsType)) {
                            return sprintf('Each item must be type of %s, %s given (offset: %s)',
                                $itemsType, ('object' == $valueType = gettype($value))
                                    ? get_class($value) : self::get($value), $offset);
                        }
                    }

                    $offset++;
                }
        }

        return true;
    }

    public static function validateMapKey($key, string &$error = null): bool
    {
        if (!is_string($key)) {
            $error = 'string';
        } elseif (self::isDigit($key)) {
            $error = 'string|int';
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

    private static function isDigit($input): bool
    {
        return is_int($input) ? true : (
            is_string($input) ? ctype_digit($input) : false
        );
    }
}
