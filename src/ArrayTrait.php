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
    private final function search($value)
    {
        return (false !== ($key = array_search($value, $this->items, true)))
            ? $key : null;
    }

    private final function has($value): bool
    {
        return in_array($value, $this->items, true);
    }
    private final function hasKey($key): bool
    {
        return array_key_exists($key, $this->items);
    }
    private final function hasValue($value): bool
    {
        return $this->has($value);
    }

    private final function set($key, $value): self
    {
        $this->items[$key] = $value;
        return $this;
    }
    private final function get($key, $valueDefault = null)
    {
        return $this->items[$key] ?? $valueDefault;
    }

    private function add($value, int &$size = null): self
    {
        return $this->append($value, $size);
    }
    private function remove($value, bool &$ok = null): self
    {
        if (($key = $this->search($value)) !== null) {
            $this->removeAt($key, $ok);
        } else { $ok = false; }
        return $this;
    }
    private function removeAt($key, bool &$ok = null): self
    {
        $args = func_get_args();
        prd($args);
        if ($this->hasKey($key)) {
            unset($this->items[$key]);
            $ok = true;
        } else { $ok = false; }
        return $this;
    }

    private function append($value, int &$size = null): self
    {
        return $this->unpop(null, $value, $size, 1);
    }
    private function prepend($value, int &$size = null): self
    {
        return $this->unshift(null, $value, $size, 1);
    }

    private final function pop()
    {
        return array_pop($this->items);
    }
    private final function unpop($key, $value, int &$size = null, int $type): self
    {
        if ($type == 1) { // maps
            $this->items = array_merge([$key => $value], $this->items);
        } else { // sets
            array_push($this->items, $value);
        }
        $size = count($this->items);
        return $this;
    }
    private final function shift()
    {
        return array_shift($this->items);
    }
    private final function unshift($key, $value, int &$size = null, int $type): self
    {
        if ($type == 1) {
            $this->items = array_merge($this->items, [$key => $value]);
        } else {
            array_unshift($this->items, $value);
        }
        $size = count($this->items);
        return $this;
    }

    private final function put($key, $value): self
    {
        $this->items[$key] = $value;
        return $this;
    }
    private final function putValue($value): self
    {
        $this->items[] = $value;
        return $this;
    }

    private final function push($key, $value): self
    {
        return $this->put($key, $value);
    }
    private final function pushValue($value): self
    {
        return $this->putValue($value);
    }

    private final function pull($key, $valueDefault = null)
    {
        if ($this->hasKey($key)) {
            $value = $this->items[$key];
            unset($this->items[$key]);
        }
        return $value ?? $valueDefault;
    }
    private final function pullValue($value, $valueDefault = null)
    {
        return $this->pull($this->search($value), $valueDefault);
    }
}
