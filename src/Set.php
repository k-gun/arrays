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

namespace xobjects;

use xobjects\{TypedArray, Type};

/**
 * @package xobjects
 * @object  xobjects\Set
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Set extends TypedArray
{
    public function __construct(array $items = null, string $itemsType = null, string $type = null,
        bool $readOnly = false, bool $allowNulls = false)
    {
        self::$notAllowedMethods = [
            /* base methods */ 'flip'
        ];

        parent::__construct($type ?? Type::SET, $items, $itemsType, $readOnly, $allowNulls);
    }

    public function search($value) { return $this->_search($value); }
    public function searchLast($value) { return $this->_searchLast($value); }
    public function indexOf($value): ?int { return $this->_indexOf($value); }
    public function lastIndexOf($value): ?int { return $this->_lastIndexOf($value); }

    public function has($value): bool { return $this->_has($value); }
    public function hasKey(int $key): bool { return $this->_hasKey($key); }

    public function set(int $key, $value, int &$size = null): self { return $this->_set($key, $value, $size); }
    public function get(int $key, $valueDefault = null, bool &$ok = null) { return $this->_get($key, $valueDefault, $ok); }

    public function add($value): self { return $this->_add($value); }
    public function remove($value, bool &$ok = null): self { return $this->_remove($value, $ok); }
    public function removeAt(int $key, bool &$ok = null): self { return $this->_removeAt($key, $ok); }
    public function removeAll(array $values, int &$count = null): self { return $this->_removeAll($values, $count); }

    public function append($value, int &$size = null): self { return $this->_append($value, $size); }
    public function prepend($value, int &$size = null): self { return $this->_prepend($value, $size); }

    public function pop(int &$size = null) { return $this->_pop($size); }
    public function unpop($value, int &$size = null): self { return $this->_unpop($value, $size); }

    public function shift(int &$size = null) { return $this->_shift($size); }
    public function unshift($value, int &$size = null): self { return $this->_unshift($value, $size); }

    public function put(int $key, $value): self { return $this->_put($key, $value); }
    public function push(int $key, $value): self { return $this->_push($key, $value); }
    public function pull(int $key, $valueDefault = null, bool &$ok = null) { return $this->_pull($key, $valueDefault, $ok); }

    public function find(Closure $func) { return $this->_find($func); }
    public function findKey(Closure $func) { return $this->_findKey($func); }
    public function findIndex(Closure $func) { return $this->_findIndex($func); }

    public function replace($value, $replaceValue, bool &$ok = null): self { return $this->_replace($value, $replaceValue, $ok); }
    public function replaceAt(int $key, $replaceValue, bool &$ok = null): self { return $this->_replaceAt($key, $replaceValue, $ok); }

    public function pad(int $times, $value, int $offset = null): self { return $this->_pad($times, $value, $offset); }
}
