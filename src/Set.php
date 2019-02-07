<?php
declare(strict_types=1);

namespace arrays;

use arrays\{AbstractArray, Type};
use arrays\exception\{TypeException, ArgumentTypeException};

/**
 * @package arrays
 * @object  arrays\Set
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Set extends TypedArray
{
    public function __construct(string $type = null, array $items = null, string $itemsType = null)
    {
        parent::__construct($type ?? Type::SET, $items, $itemsType);
    }

    // public function search($value) { return $this->_search($value); }

    // public function has($value): bool { return $this->_has($value); }
    // public function hasKey(string $key): bool { return $this->_hasKey($key); }
    // public function hasValue($value): bool { return $this->_hasValue($value); }
}
