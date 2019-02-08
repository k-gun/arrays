<?php
declare(strict_types=1);

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
        foreach ($this as $key => $_value) {
            // make a strict search
            if ($value === $_value) { return $key; }
        }
        return null;
    }

    protected final function _indexOf($value): ?int
    {
        $index = 0;
        foreach ($this->getArrayCopy() as $key => $value) {
            //
        }
    }
    protected final function _lastIndexOf($value): ?int
    {}

    protected final function _has($value): bool
    {
        return in_array($value, $this->values(), true);
    }
    protected final function _hasKey($key): bool
    {
        return in_array($value, $this->values(), true);
    }
    // @alias
    protected final function _hasValue($value): bool
    {
        return $this->_has($value);
    }

    protected final function _set($key, $value): self
    {
        $this->readOnlyCheck();
        $this->offsetSet($key, $value);
        return $this;
    }
    protected final function _get($key, $valueDefault = null, &$ok = null)
    {
        if ($ok = $this->offsetExists($key)) {
            $value = $this->offsetGet($key);
        }
        return $value ?? $valueDefault;
    }

    protected function _add($value, &$size = null): self
    {
        return $this->_append($value, $size);
    }
    protected function _remove($value, &$ok = null): self
    {
        $this->readOnlyCheck();
        if ($ok = (($key = $this->_search($value)) !== null)) {
            $this->offsetUnset($key);
        }
        return $this;
    }
    protected final function _removeAt($key, &$ok = null): self
    {
        $this->readOnlyCheck();
        if ($ok = $this->offsetExists($key)) {
            $this->offsetUnset($key);
        }
        return $this;
    }

    protected final function _append($value, &$size = null): self
    {
        return $this->_unpop($value, $size);
    }
    protected final function _prepend($value, &$size = null): self
    {
        return $this->_unshift($value, $size);
    }
    protected final function _delete($value, &$size = null): self
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
    protected final function _unpop($value, &$size = null): self
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
    protected final function _unshift($key, $value, &$size = null): self
    {
        $this->readOnlyCheck();
        // ...
        $size = array_unshift($this->items, $value);
        return $this;
    }

    protected final function _put($key, $value): self
    {
        $this->readOnlyCheck();
        // ...
        $this->items[$key] = $value;
        return $this;
    }
    protected final function _push($key, $value): self
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
