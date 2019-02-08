<?php
declare(strict_types=1);

namespace arrays;

use arrays\{AbstractArray, Type};
use arrays\exception\TypeException;

/**
 * @package arrays
 * @object  arrays\Set
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Set extends TypedArray
{
    public function __construct(string $type = null, array $items = null, string $itemsType = null,
        bool $readOnly = false, bool $allowNulls = false)
    {
        parent::__construct($type ?? Type::SET, $items, $itemsType, $readOnly, $allowNulls);
    }
}
