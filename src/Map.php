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

use xo\{TypedArray, Type};

/**
 * @package xo
 * @object  xo\Map
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Map extends TypedArray
{
    /**
     * Constructor.
     * @param array|null  $items
     * @param string|null $itemsType
     * @param bool        $readOnly
     * @param bool        $allowNulls
     */
    public function __construct(array $items = null, string $itemsType = null,
        bool $readOnly = false, bool $allowNulls = false)
    {
        self::$notAllowedMethods = [
            /* base methods */ 'flip',
            'add', 'append', 'prepend', 'unpop', 'unshift', 'flip', 'pad'
        ];

        parent::__construct(Type::MAP, $items, $itemsType, $readOnly, $allowNulls);
    }

    /**
     * Search.
     * @param  any $value
     * @return int|string|null
     */
    public function search($value)
    {
        return $this->_search($value);
    }

    /**
     * Search last.
     * @param  any $value
     * @return int|string|null
     */
    public function searchLast($value)
    {
        return $this->_searchLast($value);
    }

    /**
     * Index of.
     * @param  any $value
     * @return ?int
     */
    public function indexOf($value): ?int
    {
        return $this->_indexOf($value);
    }

    /**
     * Last index of.
     * @param  ant $value
     * @return ?int
     */
    public function lastIndexOf($value): ?int
    {
        return $this->_lastIndexOf($value);
    }

    /**
     * Has.
     * @param  any $value
     * @return bool
     */
    public function has($value): bool
    {
        return $this->_has($value);
    }

    /**
     * Has key.
     * @param  string $key
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return $this->_hasKey($key);
    }

    /**
     * set.
     * @param  string    $key
     * @param  any       $value
     * @param  int|null &$size
     * @return self
     */
    public function set(string $key, $value, int &$size = null): self
    {
        return $this->_set($key, $value, $size);
    }

    /**
     * Get
     * @param  string     $key
     * @param  any|null   $valueDefault
     * @param  bool|null &$found
     * @return any|null
     */
    public function get(string $key, $valueDefault = null, bool &$found = null)
    {
        return $this->_get($key, $valueDefault, $found);
    }

    /**
     * Remove.
     * @param  any        $value
     * @param  bool|null &$found
     * @return self
     */
    public function remove($value, bool &$found = null): self
    {
        return $this->_remove($value, $found);
    }

    /**
     * Remove at.
     * @param  string     $key
     * @param  bool|null &$found
     * @return self
     */
    public function removeAt(string $key, bool &$found = null): self
    {
        return $this->_removeAt($key, $found);
    }

    /**
     * Remove all.
     * @param  array     $values
     * @param  int|null &$count
     * @return self
     */
    public function removeAll(array $values, int &$count = null): self
    {
        return $this->_removeAll($values, $count);
    }

    /**
     * Pop.
     * @param  int|null &$size
     * @return any
     */
    public function pop(int &$size = null)
    {
        return $this->_pop($size);
    }

    /**
     * Shift.
     * @param  int|null &$size
     * @return any
     */
    public function shift(int &$size = null)
    {
        return $this->_shift($size);
    }

    /**
     * Push.
     * @param  string $key
     * @param  any    $value
     * @return self
     */
    public function push(string $key, $value): self
    {
        return $this->_push($key, $value);
    }

    /**
     * Pull.
     * @param  string     $key
     * @param  any|null   $valueDefault
     * @param  bool|null &$found
     * @return any|null
     */
    public function pull(string $key, $valueDefault = null, bool &$found = null)
    {
        return $this->_pull($key, $valueDefault, $found);
    }

    /**
     * Replace.
     * @param  any        $value
     * @param  any        $replaceValue
     * @param  bool|null &$found
     * @return self
     */
    public function replace($value, $replaceValue, bool &$found = null): self
    {
        return $this->_replace($value, $replaceValue, $found);
    }

    /**
     * Replace at.
     * @param  string      $key
     * @param  any         $replaceValue
     * @param  bool|null  &$found
     * @return self
     */
    public function replaceAt(string $key, $replaceValue, bool &$found = null): self
    {
        return $this->_replaceAt($key, $replaceValue, $found);
    }
}
