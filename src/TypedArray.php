<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Type, AbstractArray };
use arrays\exception\{ TypeException };

/**
 * @package arrays
 * @object  arrays\Map
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class TypedArray extends AbstractArray
{
    protected $type;
    protected $readOnly;
    protected $allowNulls;

    public function __construct(string $type, array $items = null, string $itemsType = null,
        bool $readOnly = false, bool $allowNulls = false)
    {
        $this->type = $type;
        $this->readOnly = $readOnly;
        $this->allowNulls = $allowNulls;

        if ($type != Type::ANY && $items != null) {
            if (!Type::validateItems($this, $items, $itemsType, $error)) {
                throw new TypeException($error);
            }
        }

        parent::__construct($type, $items);
    }

    public final function type(): string { return $this->type; }
    public final function readOnly(): bool { return $this->readOnly; }
    public final function allowNulls(): bool { return $this->allowNulls; }
}
