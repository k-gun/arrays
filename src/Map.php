<?php
declare(strict_types=1);

namespace arrays;

use arrays\{AbstractArray, Type};
use arrays\exception\{TypeException, ArgumentTypeException};

/**
 * @package arrays
 * @object  arrays\Map
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Map extends AbstractArray
{
    public function __construct(array $items = null, string $itemsType = null)
    {
        $items = $items ?? [];
        $itemsType = $itemsType ?? Type::MAP;

        // no check for subclass(es)
        $checked = (self::class !== static::class);
        if (!$checked && !Type::validateItems($items, $itemsType, $error)) {
            throw new TypeException($error);
        }

        parent::__construct($items, $itemsType);
    }

    public function search($value)
    {
        return $this->_search($value);
    }

    public function has($value): bool { return $this->_has($value); }
    public function hasKey($key): bool {
        if (!Type::validateArgumentTypeForMap($key, 1, $error)) {
            throw new ArgumentTypeException($error);
        }
        return $this->_hasKey($key);
    }
    public function hasValue($value): bool { return $this->_hasValue($value); }
}
