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
declare(strict_types=0);

namespace xo;

use xo\ArrayInterface;

/**
 * @package xo
 * @object  xo\ArrayTrait
 * @author  Kerem Güneş <k-gun@mail.com>
 */
trait ArrayTrait
{
    /**
     * Index (memory-wise index, key search).
     * @param  any  $searchValue
     * @param  bool $reverse
     * @return ?array
     */
    private final function index($searchValue, bool $reverse): ?array
    {
        if (!$reverse) {
            $index = 0;
            foreach ($this->generate() as $key => $value) {
                if ($value === $searchValue) {
                    return [$index, $key];
                }
                $index++;
            }
        } else {
            $index = $this->size() - 1;
            foreach ($this->generate(true) as $key => $value) {
                if ($value === $searchValue) {
                    return [$index, $key];
                }
                $index--;
            }
        }
        return null;
    }

    /**
     * Search.
     * @param  any $value
     * @return int|string|null
     */
    protected final function _search($value)
    {
        return $this->index($value, false)[1] ?? null;
    }

    /**
     * Search last.
     * @param  any $value
     * @return int|string|null
     */
    protected final function _searchLast($value)
    {
        return $this->index($value, true)[1] ?? null;
    }

    /**
     * Index of.
     * @param  any $value
     * @return ?int
     */
    protected final function _indexOf($value): ?int
    {
        return $this->index($value, false)[0] ?? null;
    }

    /**
     * Last index of.
     * @param  any $value
     * @return ?int
     */
    protected final function _lastIndexOf($value): ?int
    {
        return $this->index($value, true)[0] ?? null;
    }

    /**
     * Has.
     * @param  any $value
     * @return bool
     */
    protected final function _has($value): bool
    {
        return $this->_indexOf($value) !== null;
    }

    /**
     * Has key.
     * @param  int|string $key
     * @param  bool       $keyCheck @internal
     * @return bool
     */
    protected final function _hasKey($key, bool $keyCheck = true): bool
    {
        $keyCheck && $this->keyCheck($key);

        return in_array($key, $this->keys(), true);
    }

    /**
     * Set.
     * @param  int|string $key
     * @param  any        $value
     * @param  int|null  &$size
     * @return xo\ArrayInterface
     */
    protected final function _set($key, $value, int &$size = null): ArrayInterface
    {
        $this->keyCheck($key);
        $this->executeCommand('set', $key, $value, $size);

        return $this;
    }

    /**
     * Get.
     * @param  int|string  $key
     * @param  any|null    $valueDefault
     * @param  bool|null  &$found
     * @return any|null
     */
    protected final function _get($key, $valueDefault = null, bool &$found = null)
    {
        $this->keyCheck($key);

        if ($found = $this->_hasKey($key, false)) {
            $this->executeCommand('get', $key, $value);
        }

        return $value ?? $valueDefault;
    }

    /**
     * Add.
     * @param  any $value
     * @return xo\ArrayInterface
     */
    protected final function _add($value): ArrayInterface
    {
        return $this->_unpop($value);
    }

    /**
     * Remove.
     * @param  any        $value
     * @param  bool|null &$found
     * @return xo\ArrayInterface
     */
    protected final function _remove($value, bool &$found = null): ArrayInterface
    {
        if ($found = (null !== $key = $this->_search($value))) {
            $this->executeCommand('unset', $key);
        }

        return $this;
    }

    /**
     * Remove at.
     * @param  int|string  $key
     * @param  bool|null  &$found
     * @return xo\ArrayInterface
     */
    protected final function _removeAt($key, bool &$found = null): ArrayInterface
    {
        $this->keyCheck($key);

        if ($found = $this->_hasKey($key, false)) {
            $this->executeCommand('unset', $key);
        }

        return $this;
    }

    /**
     * Remove all.
     * @param  array     $values
     * @param  int|null &$count
     * @return xo\ArrayInterface
     */
    protected final function _removeAll(array $values, int &$count = null): ArrayInterface
    {
        foreach ($values as $value) {
            while (null !== $key = $this->_search($value)) {
                $this->executeCommand('unset', $key);
                $count++;
            }
        }

        return $this;
    }

    /**
     * Append.
     * @param  any       $value
     * @param  int|null &$size
     * @return xo\ArrayInterface
     */
    protected final function _append($value, int &$size = null): ArrayInterface
    {
        return $this->_unpop($value, $size);
    }

    /**
     * Prepend.
     * @param  any       $value
     * @param  int|null &$size
     * @return xo\ArrayInterface
     */
    protected final function _prepend($value, &$size = null): ArrayInterface
    {
        return $this->_unshift($value, $size);
    }

    /**
     * Pop.
     * @param  int|null &$size
     * @return any
     */
    protected final function _pop(int &$size = null)
    {
        $this->executeCommand('pop', $value, $size);

        return $value;
    }

    /**
     * Unpop.
     * @param  any       $value
     * @param  int|null &$size
     * @return xo\ArrayInterface
     */
    protected final function _unpop($value, int &$size = null): ArrayInterface
    {
        $this->executeCommand('unpop', $value, $size);

        return $this;
    }

    /**
     * Shift.
     * @param  int|null &$size
     * @return any
     */
    protected final function _shift(int &$size = null)
    {
        $this->executeCommand('shift', $value, $size);

        return $value;
    }

    /**
     * Unshift.
     * @param  any       $value
     * @param  int|null &$size
     * @return xo\ArrayInterface
     */
    protected final function _unshift($value, int &$size = null): ArrayInterface
    {
        $this->executeCommand('unshift', $value, $size);

        return $this;
    }

    /**
     * Put.
     * @param  int|string $key
     * @param  any        $value
     * @return xo\ArrayInterface
     */
    protected final function _put($key, $value): ArrayInterface
    {
        $this->keyCheck($key);
        $this->executeCommand('put', $key, $value);

        return $this;
    }

    /**
     * Push.
     * @param  int|string $key
     * @param  any        $value
     * @return xo\ArrayInterface
     */
    protected final function _push($key, $value): ArrayInterface
    {
        $this->keyCheck($key);
        $this->executeCommand('push', $key, $value);

        return $this;
    }

    /**
     * Pull.
     * @param  int|string  $key
     * @param  any|null    $valueDefault
     * @param  bool|null  &$found
     * @return any|null
     */
    protected final function _pull($key, $valueDefault = null, bool &$found = null)
    {
        $this->keyCheck($key);

        if ($found = $this->_hasKey($key, false)) {
            $this->executeCommand('pull', $key, $value);
        }

        return $value ?? $valueDefault;
    }

    /**
     * Replace.
     * @param  any        $value
     * @param  any        $replaceValue
     * @param  bool|null &$found
     * @return xo\ArrayInterface
     */
    protected final function _replace($value, $replaceValue, bool &$found = null): ArrayInterface
    {
        if ($found = (null !== $key = $this->_search($value))) {
            $this->_put($key, $replaceValue);
        }

        return $this;
    }

    /**
     * Replace at.
     * @param  int|string  $key
     * @param  any         $replaceValue
     * @param  bool|null  &$found
     * @return xo\ArrayInterface
     */
    protected final function _replaceAt($key, $replaceValue, bool &$found = null): ArrayInterface
    {
        $this->keyCheck($key);

        if ($found = $this->_hasKey($key, false)) {
            $this->_put($key, $replaceValue);
        }

        return $this;
    }

    /**
     * Pad.
     * @param  int      $times
     * @param  any      $value
     * @param  int|null $offset
     * @return xo\ArrayInterface
     */
    protected final function _pad(int $times, $value, int $offset = null): ArrayInterface
    {
        if ($offset === null) {
            while ($times--) {
                $this->_unpop($value);
            }
        } else {
            for ($i = 0; $i < $times; $i++) {
                $this->_set($offset + $i, $value);
            }
        }

        return $this;
    }
}
