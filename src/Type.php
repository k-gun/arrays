<?php
declare(strict_types=1);

namespace arrays;

use Error;
use arrays\{Map, Set, Tuple};
use function arrays\{is_sequential_array, is_associative_array};

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
                 SET = 'Set',
                 TUPLE = 'Tuple';


    public static function validateItems(object $array, array $items, string $itemsType = null, string &$error = null): bool
    {
        static $mapMessage = '%s() objects accept associative arrays with string keys only, invalid items given';
        static $setMessage = '%s() objects accept non-associative items with int keys only, invalid items given';
        static $valueMessage = 'All values of %s() must be type of %s, %s given (offset: %s, value: %s)';
        static $nullValueMessage = '%s() object do not accept null values, null given (offset: %s)';

        $type = $array->type();
        $allowNulls = $array->allowNulls();
        $typeBasic = self::isBasic($type); $isMapLike = $isSetLike = false;
        if (!$typeBasic) {
            if ($array instanceof Map) {
                $isMapLike = true;
                if (!is_associative_array($items)) { $error = sprintf($mapMessage, $type); return false; }
            } elseif ($array instanceof Set || $array instanceof Tuple) {
                $isSetLike = true;
                if (!is_sequential_array($items)) { $error = sprintf($setMessage, $type); return false; }
            }
        }

        $offset = 0;
        foreach ($items as $key => $value) {
            if ($value === null) {
                if ($allowNulls) {
                    $offset++; continue; }
                $error = sprintf($nullValueMessage, $array->getShortName(), $offset);
                return false;
            }
            $valueType = self::get($value);
            if ($typeBasic) {
                if ($valueType != $type) {
                    $error = sprintf($valueMessage, $array->getShortName(), $type, $valueType, $offset,
                        self::export($value));
                    return false;
                }
            } elseif ($isMapLike || $isSetLike) {
                if ($itemsType != null) {
                    $itemsTypeBasic = self::isBasic($itemsType);
                    if ($itemsTypeBasic) {
                        if ($valueType != $itemsType) {
                            $error = sprintf($valueMessage, $array->getShortName(), $itemsType, $valueType, $offset,
                                self::export($value));
                            return false;
                        }
                    } elseif (!is_a($value, $itemsType)) {
                        $error = sprintf($valueMessage, $array->getShortName(), $itemsType,
                            ($valueType == 'object' ? get_class($value) : $valueType), $offset, self::export($value));
                        return false;
                    }
                }
            } elseif (!is_a($value, $type)) {
                $error = sprintf($valueMessage, $array->getShortName(), $type,
                    ($valueType == 'object' ? get_class($value) : $valueType), $offset, self::export($value));
                return false;
            }
            $offset++;
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
        if (is_null($input))   return 'null';
        if (is_scalar($input)) return var_export($input, true);
        if (is_array($input))  return 'array';
        if (is_object($input)) return 'object('. get_class($input) .')';
        return $input;
    }

    public static function toArray($input): array
    {
        return (array) ($input ?: []);
    }
    public static function toObject($input): object
    {
        return (object) self::toArray($input);
    }

    public static function isBasic(string $type): bool
    {
        return in_array(strtolower($type), ['int', 'float', 'string', 'bool', 'array', 'object']);
    }

    public static function isDigit($input): bool
    {
        if (is_int($input)) {
            return true;
        } elseif (is_string($input) && ctype_digit($input)) {
            return true;
        } elseif ($complex && is_numeric($input)) {
            $input = (string) $input;
            if (strpos($input, '.') === false && ($input < 0)) {
                return true;
            }
        }
        return false;
    }

    public static function isMapLike($input): bool
    {
        try {
            return is_object($input) && $input instanceof Map;
            // return is_object($input) && (false !== strpos($input->type(), 'Map'));
        } catch (Error $e) { return false; }
    }
    public static function isSetLike($input): bool
    {
        try {
            return is_object($input) && $input instanceof Set;
            // return is_object($input) && (false !== strpos($input->type(), 'Set'));
        } catch (Error $e) { return false; }
    }
}
