<?php
declare(strict_types=0);

namespace arrays;

/**
 * @package arrays
 * @object  arrays\ArrayTrait
 * @author  Kerem Güneş <k-gun@mail.com>
 */
trait ArrayTrait
{
    // memory-wise index, key search
    private final function _index($searchValue, $reverse)
    {
        if (!$reverse) { $index = 0;
            foreach ($this->generate() as $key => $value) {
                if ($value === $searchValue) { return [$index, $key]; } $index++;
            }
        } else { $index = $this->size() - 1;
            foreach ($this->generateReverse() as $key => $value) {
                if ($value === $searchValue) { return [$index, $key]; } $index--;
            }
        }
        return null;
    }
    // @return int|string|null
    protected final function _search($value)
    {
        return $this->_index($value, false)[1] ?? null;
    }
    protected final function _searchLast($value)
    {
        return $this->_index($value, true)[1] ?? null;
    }
    protected final function _indexOf($value)
    {
        return $this->_index($value, false)[0] ?? null;
    }
    protected final function _lastIndexOf($value)
    {
        return $this->_index($value, true)[0] ?? null;
    }

    protected final function _has($value)
    {
        return $this->_indexOf($value) !== null;
    }
    protected final function _hasKey($key)
    {
        return in_array($key, $this->keys(), true);
    }
    // @alias
    protected final function _hasValue($value)
    {
        return $this->_has($value);
    }

    protected final function _set($key, $value, &$size = null)
    {
        $this->stackCommand('set', $key, $value, $size);
        return $this;
    }
    protected final function _get($key, $valueDefault = null, &$ok = null)
    {
        if ($ok = $this->_hasKey($key)) {
            $this->stackCommand('get', $key, $value);
        }
        return $value ?? $valueDefault;
    }

    protected final function _add($value)
    {
        return $this->_unpop($value);
    }
    protected final function _remove($value, &$ok = null)
    {
        if ($ok = (null !== $key = $this->_search($value))) {
            $this->stackCommand('unset', $key);
        }
        return $this;
    }
    protected final function _removeAt($key, &$ok = null)
    {
        if ($ok = $this->_hasKey($key)) {
            $this->stackCommand('unset', $key);
        }
        return $this;
    }
    protected final function _removeAll($values, &$count = null)
    {
        foreach ($values as $value) {
            while (null !== $key = $this->_search($value)) {
                $this->stackCommand('unset', $key);
                $count++;
            }
        }
        return $this;
    }

    protected final function _append($value, &$size = null)
    {
        return $this->_unpop($value, $size);
    }
    protected final function _prepend($value, &$size = null)
    {
        return $this->_unshift($value, $size);
    }

    protected final function _pop(&$size = null)
    {
        $this->stackCommand('pop', $value, $size);
        return $value;
    }
    protected final function _unpop($value, &$size = null)
    {
        $this->stackCommand('unpop', $value, $size);
        return $this;
    }
    protected final function _shift(&$size = null)
    {
        $this->stackCommand('shift', $value, $size);
        return $value;
    }
    protected final function _unshift($value, &$size = null)
    {
        $this->stackCommand('unshift', $value, $size);
        return $this;
    }

    protected final function _put($key, $value)
    {
        $this->stackCommand('put', $key, $value);
        return $this;
    }
    protected final function _push($key, $value)
    {
        $this->stackCommand('push', $key, $value);
        return $this;
    }
    protected final function _pull($key, $valueDefault = null, &$ok = null)
    {
        if ($ok = $this->_hasKey($key)) {
            $this->stackCommand('pull', $key, $value);
        }
        return $value ?? $valueDefault;
    }

    //
}
