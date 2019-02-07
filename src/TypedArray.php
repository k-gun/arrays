<?php
declare(strict_types=1);

namespace arrays;

use arrays\{AbstractArray, Type, TypeException};

/**
 * @package arrays
 * @object  arrays\Map
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class TypedArray extends AbstractArray
{
    protected $type;

    public function __construct(string $type, array $items = null, string $itemsType = null)
    {
        $this->type = $type;
        $items = $items ?? [];

        if (!Type::validateItems($this, $items, $itemsType, $error)) {
            throw new TypeException($error);
        }

        parent::__construct($type, $items);
    }

    public final function type(): string { return $this->type; }
}
