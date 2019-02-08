<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Type, TypedArray };
// use arrays\exception\{ MethodException };

/**
 * @package arrays
 * @object  arrays\AnyArray
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class AnyArray extends TypedArray
{
    public function __construct(array $items = null, string $itemsType = null,
        bool $readOnly = false, bool $allowNulls = true)
    {
        parent::__construct(Type::ANY, $items, $itemsType, $readOnly, $allowNulls);
    }

    public function search($value) { return $this->_search($value); }
    public function indexOf($value): ?int { return $this->_indexOf($value); }
    public function lastIndexOf($value): ?int { return $this->_lastIndexOf($value); }

    public function has($value): bool { return $this->_has($value); }
    public function hasKey($key): bool { return $this->_hasKey($key); }
    public function hasValue($value): bool { return $this->_hasValue($value); }

    public function set($key, $value, int &$size = null): self { return $this->_set($key, $value, $size); }
    public function get($key, $valueDefault = null, bool &$ok = null) { return $this->_get($key, $valueDefault, $ok); }

    public function add($value) { return $this->_add($value); }
    public function remove($value, bool &$ok = null): self { return $this->_remove($value, $ok); }
    public function removeAt($key, bool &$ok = null): self { return $this->_removeAt($key, $ok); }
    public function removeAll(array $values, int &$count = null): self { return $this->_removeAll($values, $count); }

    public function append($value, int &$size = null): self { return $this->_append($value, $size); }
    public function prepend($value, int &$size = null): self { return $this->_prepend($value, $size); }

    public function pop(int &$size = null) { return $this->_pop($size); }
    public function unpop($value, int &$size = null): self { return $this->_unpop($value, $size); }

    public function shift(int &$size = null) { return $this->_shift($size); }
    public function unshift($value, int &$size = null): self { return $this->_unshift($value, $size); }

    public function put($key, $value): self { return $this->_put($key, $value); }
    public function push($key, $value): self { return $this->_push($key, $value); }
    public function pull($key, $valueDefault = null, bool &$ok = null) { return $this->_pull($key, $valueDefault, $ok); }
}
