<?php
declare(strict_types=1);

namespace arrays;

use arrays\{Type, Map};
use arrays\exception\TypeException;

/**
 * @package arrays
 * @object  arrays\IntMap
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class IntMap extends Map
{
    public function __construct(array $items = null)
    {
        $items = $items ?? [];
        $itemsType = Type::INT_MAP;

        if (!Type::validateItems($items, $itemsType, $error)) {
            throw new TypeException($error);
        }

        parent::__construct($items, $itemsType);
    }
}
