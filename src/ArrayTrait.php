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
        return (false !== ($key = array_search($value, $this->items, true)))
            ? $key : null;
    }

    protected final function _has($value): bool
    {
        return in_array($value, $this->items, true);
    }
    protected final function _hasKey($key): bool
    {
        return array_key_exists($key, $this->items);
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
    protected final function _get($key, $valueDefault = null)
    {
        return $this->items[$key] ?? $valueDefault;
    }

    protected final function _pop()
    {
        return array_pop($this->items);
    }
    protected final function _unpop($key, $value, $type): self
    {
        if ($type == 1) { // maps
            $this->items = array_merge([$key => $value], $this->items);
        } else { // sets
            array_push($this->items, $value);
        }
        return $this;
    }
    protected final function _shift()
    {
        return array_shift($this->items);
    }
    protected final function _unshift($key, $value, $type): self
    {
        if ($type == 1) {
            $this->items = array_merge($this->items, [$key => $value]);
        } else {
            array_unshift($this->items, $value);
        }
        return $this;
    }

    protected final function _put($key, $value): self
    {
        $this->items[$key] = $value;
        return $this;
    }
    protected final function _putValue($value): self
    {
        $this->items[] = $value;
        return $this;
    }

    protected final function _push($key, $value): self
    {
        return $this->_put($key, $value);
    }
    protected final function _pushValue($value): self
    {
        return $this->_putValue($value);
    }

    protected final function _pull($key, $valueDefault = null)
    {
        if ($this->_hasKey($key)) {
            $value = $this->items[$key];
            unset($this->items[$key]);
        }
        return $value ?? $valueDefault;
    }
    protected final function _pullValue($value, $valueDefault = null)
    {
        return $this->_pull($this->_search($value), $valueDefault);
    }
}
