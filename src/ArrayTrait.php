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
    // @return int|string|null
    protected final function _search($value)
    {
        return (false !== $key = array_search($value, $this->toArray(), true)) ? $key : null;
    }

    private final function _index($value, $_values, $reverse)
    {
        $index = $reverse ? count($_values) - 1 : 0;
        foreach ($_values as $_value) {
            if ($_value === $value) { return $index; }
            $reverse ? $index-- : $index++;
        }
        return null;
    }
    protected final function _indexOf($value)
    {
        return $this->_index($value, $this->values(), false);
    }
    protected final function _lastIndexOf($value)
    {
        return $this->_index($value, array_reverse($this->values()), true);
    }

    protected final function _has($value)
    {
        return in_array($value, $this->values(), true);
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

    protected final function _set($key, $value)
    {
        $this->readOnlyCheck();
        $this->offsetSet($key, $value);
        return $this;
    }
    protected final function _get($key, $valueDefault = null, &$ok = null)
    {
        if ($ok = $this->_hasKey($key)) {
            $value = $this->offsetGet($key);
        }
        return $value ?? $valueDefault;
    }

    protected function _add($value, &$size = null)
    {
        return $this->_append($value, $size);
    }
    protected function _remove($value, &$ok = null)
    {
        $this->readOnlyCheck();
        if ($ok = (($key = $this->_search($value)) !== null)) {
            $this->offsetUnset($key);
        }
        return $this;
    }
    protected final function _removeAt($key, &$ok = null)
    {
        $this->readOnlyCheck();
        if ($ok = $this->_hasKey($key)) {
            $this->offsetUnset($key);
        }
        return $this;
    }
    protected final function _removeAll($values, &$count = null)
    {
        foreach ($values as $value) {
            while ($this->remove($value, $ok) && $ok) { $count++; }
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
    protected final function _delete($value, &$size = null)
    {
        $this->_remove($value, $ok);
        $size = $this->size();
        return $this;
    }

    protected final function _pop(&$size = null)
    {
        $this->readOnlyCheck();
        $value = array_pop($this->items);
        $size = $this->size();
        return $value;
    }
    protected final function _unpop($value, &$size = null)
    {
        $this->readOnlyCheck();
        // ...
        $size = array_push($this->items, $value);
        return $this;
    }
    protected final function _shift(&$size = null)
    {
        $this->readOnlyCheck();
        // ...
        $value = array_shift($this->items);
        $size = $this->size();
        return $value;
    }
    protected final function _unshift($key, $value, &$size = null)
    {
        $this->readOnlyCheck();
        // ...
        $size = array_unshift($this->items, $value);
        return $this;
    }

    protected final function _put($key, $value)
    {
        $this->readOnlyCheck();
        // ...
        $this->items[$key] = $value;
        return $this;
    }
    protected final function _push($key, $value)
    {
        $this->readOnlyCheck();
        // ...
        unset($this->items[$key]);
        $this->items[$key] = $value;
        return $this;
    }
    protected final function _pull($key, $valueDefault = null, &$ok = null)
    {
        $this->readOnlyCheck();
        // ...
        if (array_key_exists($key, $this->items)) {
            $value = $this->items[$key];
            unset($this->items[$key]);
            $ok = true;
        } else { $ok = false; }
        return $value ?? $valueDefault;
    }
}
