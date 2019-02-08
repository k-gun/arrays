<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Type, TypedArray };
// use arrays\exception\{ MethodException };

/**
 * @package arrays
 * @object  arrays\Tuple
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Tuple extends TypedArray
{
    public function __construct(array $items = null, string $itemsType = null, bool $allowNulls = false)
    {
        parent::__construct(Type::TUPLE, $items, $itemsType, $readOnly = true, $allowNulls);
    }
}
