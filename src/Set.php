<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Type, TypedArray };
// use arrays\exception\{ MethodException };

/**
 * @package arrays
 * @object  arrays\Set
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Set extends TypedArray
{
    public function __construct(array $items = null, string $itemsType = null, string $type = null,
        bool $readOnly = false, bool $allowNulls = false)
    {
        parent::__construct($type ?? Type::SET, $items, $itemsType, $readOnly, $allowNulls);
    }
}
