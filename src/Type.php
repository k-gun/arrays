<?php
declare(strict_types=1);

namespace arrays;

use TypeError;
use function arrays\is_digit;

/**
 * @package arrays
 * @object  arrays\Type
 * @author  Kerem Güneş <k-gun@mail.com>
 */
final /* static */ class Type
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


    public static function validateItems(array $items, string $itemsType, string &$error = null): bool
    {
        static $mapMessage = '%ss accept associative arrays with non-digit string keys,'.
            ' %s key given (offset: %s, key: %s)';
        static $setMessage = '%ss accept non-associative arrays with unsigned-int keys,'.
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

    public static function validateArgumentType($arg, int $argNum, string $argTypeMust, string &$error = null, bool $nonDigit = false): bool
    {
        static $message = 'Argument %s given to %s must be %s, %s given';
        static $getMethod;
        if ($getMethod == null) {
            $getMethod = function () {
                $trace =@ end(debug_backtrace(0));
                return sprintf('%s::%s()', $trace['class'], $trace['function']);
            };
            $message = 'Argument %s given to %s must be %s, %s given';
        }
        $argType = self::get($arg);
        if ($argType != $argTypeMust) {
            $error = sprintf($message, $argNum, $getMethod(), $argTypeMust,
                ($arg === null) ? 'null' : $argType .'('. self::export($arg) .')');
        } elseif ($nonDigit && self::isDigit($arg)) {
            $error = sprintf($message, $argNum, $getMethod(), 'non-digit', 'digit('. $arg .')');
        }
        return ($error == null);
    }
    public static function validateArgumentTypeForMap($arg, int $argNum, string &$error = null): bool
    {
        return self::validateArgumentType($arg, $argNum, 'string', $error, true);
    }

    public static function get($input): string
    {
        return strtr(gettype($input), [
            'NULL'    => 'null',
            'integer' => 'int',
            'double'  => 'float',
            'boolean' => 'bool'
        ]);
    }
    public static function export($input): string
    {
        if (is_null($input)) return 'null';
        if (is_scalar($input)) return var_export($input, true);
        if (is_array($input)) return 'array';
        if (is_object($input)) return get_class($input);
        return $input;
    }

    public static function isDigit($input): bool
    {
        return is_digit($input);
    }

    public static function isMapLike($input): bool
    {
        try {
            return strpos(get_class($input), 'arrays\Map') === 0;
        } catch (TypeError $e) { return false; }
    }
    public static function isSetLike($input): bool
    {
        try {
            return strpos(get_class($input), 'arrays\Set') === 0;
        } catch (TypeError $e) { return false; }
    }
}
