<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Type, TypedArray };
use arrays\exception\{ MethodException };

/**
 * @package arrays
 * @object  arrays\Map
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Map extends TypedArray
{
    public function __construct(array $items = null, string $itemsType = null, string $type = null,
        bool $readOnly = false, bool $allowNulls = false)
    {
        parent::__construct($type ?? Type::MAP, $items, $itemsType, $readOnly, $allowNulls);
    }

    public function search($value): ?string { return $this->_search($value); }
    public function indexOf($value): ?int { return $this->_indexOf($value); }
    public function lastIndexOf($value): ?int { return $this->_lastIndexOf($value); }

    public function has($value): bool { return $this->_has($value); }
    public function hasKey(string $key): bool { return $this->_hasKey($key); }
    public function hasValue($value): bool { return $this->_hasValue($value); }

    public function set(string $key, $value): self { return $this->_set($key, $value); }
    public function get(string $key, $valueDefault = null, bool &$ok = null) { return $this->_get($key, $valueDefault, $ok); }

    // public final function add() { throw new MethodException('Not allowed method Map::add()'); }
    public function remove($value, bool &$ok = null): self { return $this->_remove($value, $ok); }
    public function removeAt(string $key, bool &$ok = null): self { return $this->_removeAt($key, $ok); }
    public function removeAll(array $values, int &$count = null): self { return $this->_removeAll($values, $count); }

    // public final function append() { throw new MethodException('Not allowed method Map::append()'); }
    // public final function prepend() { throw new MethodException('Not allowed method Map::prepend()'); }

    public function pop(int &$size = null) { return $this->_pop($size); }
    public final function unpop() { throw new MethodException('Not allowed method Map::unpop()'); }
}
