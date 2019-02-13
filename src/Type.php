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

namespace xo;

use xo\{StaticClass, AnyArray, Map, Set, Tuple};
use xo\util\{ArrayUtil, NumberUtil};
use Error;

/**
 * @package xo
 * @object  xo\Type
 * @author  Kerem Güneş <k-gun@mail.com>
 */
final class Type extends StaticClass
{
    /**
     * Types.
     * @const string
     */
    public const ANY = 'Any',
                 MAP = 'Map',
                 SET = 'Set',
                 TUPLE = 'Tuple';

    /**
     * Basics.
     * @var array[string]
     */
    private static $basics = ['int', 'float', 'string', 'bool', 'array', 'object'];

    /**
     * Map message.
     * @var string
     */
    private static $mapMessage = '%s() objects accept associative arrays with string keys only, invalid items given';

    /**
     * Set message.
     * @var string
     */
    private static $setMessage = '%s() objects accept non-associative items with int keys only, invalid items given';

    /**
     * Value message.
     * @var string
     */
    private static $valueMessage = 'All values of %s() must be type of %s, %s given (offset: %s, value: %s)';

    /**
     * Null value message.
     * @var string
     */
    private static $nullValueMessage = '%s() object do not accept null values, null given (offset: %s)';

    /**
     * Validate items.
     * @param  object       $object
     * @param  string       $type
     * @param  array        $items
     * @param  string|null  $itemsType
     * @param  bool         $allowNulls
     * @param  string|null &$error
     * @return bool
     */
    public static function validateItems(object $object, string $type, array $items, string $itemsType = null,
        bool $allowNulls, string &$error = null): bool
    {
        $typeBasic = self::isBasic($type);
        if (!$typeBasic) {
            if (self::isMapLike($object)) {
                if (!ArrayUtil::isAssociativeArray($items)) {
                    $error = sprintf(self::$mapMessage, $type);
                    return false;
                }
            } elseif (self::isSetLike($object)) {
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
            } elseif ($itemsType && $itemsType != 'any') {
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

            $offset++;
        }

        return true;
    }

    /**
     * Get.
     * @param  any  $input
     * @param  bool $objectCheck
     * @return string
     */
    public static function get($input, bool $objectCheck = false): string
    {
        if ($objectCheck && is_object($input)) {
            return get_class($input);
        }

        return strtr(gettype($input), [
            'NULL'    => 'null',
            'integer' => 'int',
            'double'  => 'float',
            'boolean' => 'bool'
        ]);
    }

    /**
     * Export.
     * @param  any $input
     * @return string
     */
    public static function export($input): string
    {
        if (is_null($input))   return 'null';
        if (is_scalar($input)) return var_export($input, true);
        if (is_array($input))  return 'array';
        if (is_object($input)) return 'object('. get_class($input) .')';
        return (string) $input;
    }

    /**
     * Make array.
     * @param  array|object $input
     * @return array
     */
    public static function makeArray($input): array
    {
        return (array) ($input ?: []);
    }

    /**
     * Make object.
     * @param  array|object $input
     * @return object
     */
    public static function makeObject($input): object
    {
        return (object) self::makeArray($input);
    }

    /**
     * Is basic.
     * @param  string $type
     * @return bool
     */
    public static function isBasic(string $type): bool
    {
        return in_array(strtolower($type), self::$basics);
    }

    /**
     * Is digit
     * @param  [type] $input
     * @param  bool   $complex
     * @return bool
     */
    public static function isDigit($input, bool $complex = true): bool
    {
        return NumberUtil::isDigit($input);
    }

    /**
     * Is tuple.
     * @param  any $input
     * @return bool
     */
    public static function isTuple($input): bool
    {
        return is_object($input) && $input instanceof Tuple;
    }

    /**
     * Is map like.
     * @param  any $input
     * @return bool
     */
    public static function isMapLike($input): bool
    {
        return is_object($input) && $input instanceof Map;
    }

    /**
     * Is set like.
     * @param  any $input
     * @return bool
     */
    public static function isSetLike($input): bool
    {
        return is_object($input) && ($input instanceof Set || $input instanceof Tuple);
    }
}
