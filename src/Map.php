<?php
declare(strict_types=1);

namespace arrays;

use arrays\{AbstractArray, Type};
use arrays\exception\TypeException;

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
        if (!$checked && !$this->validateItems($items, $itemsType, $error)) {
            throw new TypeException($error);
        }

        parent::__construct($items, $itemsType);
    }
}
