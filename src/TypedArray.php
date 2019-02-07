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
class TypedArray extends AbstractArray
{
    public function __construct(array $items = null, string $itemsType)
    {
        $items = $items ?? [];

        // no check for subclass(es)
        $checked = (self::class !== static::class);
        if (!$checked && !Type::validateItems($items, $itemsType, $error)) {
            // throw new TypeException($error);
        }

        parent::__construct($items, $itemsType);
    }
}
