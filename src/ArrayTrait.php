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
    // @return int|string
    protected final function _search($value)
    {
        foreach ($this as $_ => $_value) {
            if ($_value === $value) {
                return $value;
            }
        }
    }

    protected final function _has($value): bool
    {
        return in_array($value, $this->values(), true);
    }
    protected final function _hasKey($key): bool
    {
        return in_array($key, $this->keys(), true);
    }
    protected final function _hasValue($value): bool
    {
        return $this->_has($value);
    }

    protected final function _set($key, $value): self
    {
        $this->items[$key] = $value;
        return $this;
    }
    protected final function _get($key, $valueDefault = null, &$ok = null)
    {
        if (array_key_exists($key, $this->items)) {
            $value = $this->items[$key];
            $ok = true;
        } else { $ok = false; }
        return $value ?? $valueDefault;
    }

    protected function _add($value, &$size = null): self
    {
        return $this->_append($value, $size);
    }
    protected function _remove($value, &$ok = null): self
    {
        if (($key = $this->_search($value)) !== null) {
            $this->_removeAt($key, $ok);
        } else { $ok = false; }
        return $this;
    }
    protected final function _removeAt($key, &$ok = null): self
    {
        if (array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
            $ok = true;
        } else { $ok = false; }
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
        $size = count($this->items);
        return $this;
    }

    protected final function _pop(&$size = null)
    {
        $value = array_pop($this->items);
        $size = count($this->items);
        return $value;
    }
    protected final function _unpop($value, &$size = null): self
    {
        $size = array_push($this->items, $value);
        return $this;
    }
    protected final function _shift(&$size = null)
    {
        $value = array_shift($this->items);
        $size = count($this->items);
        return $value;
    }
    protected final function _unshift($key, $value, &$size = null): self
    {
        $size = array_unshift($this->items, $value);
        return $this;
    }

    protected final function _put($key, $value): self
    {
        $this->items[$key] = $value;
        return $this;
    }
    protected final function _push($key, $value): self
    {
        unset($this->items[$key]);
        $this->items[$key] = $value;
        return $this;
    }
    protected final function _pull($key, $valueDefault = null, &$ok = null)
    {
        if (array_key_exists($key, $this->items)) {
            $value = $this->items[$key];
            unset($this->items[$key]);
            $ok = true;
        } else { $ok = false; }
        return $value ?? $valueDefault;
    }
}
