<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2019 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

namespace objects;

use objects\StaticClass;
use objects\{AnyArray, Map, Set, Tuple};
use objects\util\ArrayUtil;
use Error;

/**
 * @package objects
 * @object  objects\Type
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Type extends StaticClass
{
    /**
     * Types.
     * const int
     */
    public const ANY = 'Any',
                 MAP = 'Map',
                 SET = 'Set',
                 TUPLE = 'Tuple';

    private static $basics = ['int', 'float', 'string', 'bool', 'array', 'object'];

    private static $mapMessage = '%s() objects accept associative arrays with string keys only, invalid items given';
    private static $setMessage = '%s() objects accept non-associative items with int keys only, invalid items given';
    private static $valueMessage = 'All values of %s() must be type of %s, %s given (offset: %s, value: %s)';
    private static $nullValueMessage = '%s() object do not accept null values, null given (offset: %s)';

    public static function validateItems(object $object, array $items, string $itemsType = null, string &$error = null): bool
    {
        $type = $object->type();
        $allowNulls = $object->allowNulls();
        $typeBasic = self::isBasic($type); $isMapLike = $isSetLike = false;
        if (!$typeBasic) {
            if ($isMapLike = self::isMapLike($object)) {
                if (!ArrayUtil::isAssociativeArray($items)) {
                    $error = sprintf(self::$mapMessage, $type);
                    return false;
                }
            } elseif ($isSetLike = self::isSetLike($object)) {
                if (!ArrayUtil::isSequentialArray($items)) {
                    $error = sprintf(self::$setMessage, $type);
                    return false;
                }
            }
        }

        $offset = 0;
        foreach ($items as $key => $value) {
            if ($value === null) {
                if ($allowNulls) {
                    $offset++; continue; }
                $error = sprintf(self::$nullValueMessage, $object->getShortName(), $offset);
                return false;
            }
            $valueType = self::get($value);
            if ($typeBasic) {
                if ($valueType != $type) {
                    $error = sprintf(self::$valueMessage, $object->getShortName(), $type, $valueType, $offset,
                        self::export($value));
                    return false;
                }
            } elseif ($isMapLike || $isSetLike) {
                if ($itemsType != null) {
                    $itemsTypeBasic = self::isBasic($itemsType);
                    if ($itemsTypeBasic) {
                        if ($valueType != $itemsType) {
                            $error = sprintf(self::$valueMessage, $object->getShortName(), $itemsType, $valueType, $offset,
                                self::export($value));
                            return false;
                        }
                    } elseif (!is_a($value, $itemsType)) {
                        $error = sprintf(self::$valueMessage, $object->getShortName(), $itemsType,
                            ($valueType == 'object' ? get_class($value) : $valueType), $offset, self::export($value));
                        return false;
                    }
                }
            } elseif (!is_a($value, $type)) {
                $error = sprintf(self::$valueMessage, $object->getShortName(), $type,
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
        return in_array(strtolower($type), self::$basics);
    }

    public static function isDigit($input, bool $complex = true): bool
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

    public static function isAny($input): bool
    {
        return is_object($input) && $input instanceof AnyArray;
    }
    public static function isTuple($input): bool
    {
        return is_object($input) && $input instanceof Tuple;
    }
    public static function isMapLike($input): bool
    {
        return is_object($input) && $input instanceof Map;
    }
    public static function isSetLike($input): bool
    {
        return is_object($input) && ($input instanceof Set || $input instanceof Tuple);
    }
}
