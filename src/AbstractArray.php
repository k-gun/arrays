<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Arrays, Type,
    ArrayTrait, ArrayInterface, ArraysException };
use arrays\exception\{
    TypeException, MethodException,
    ArgumentException, ArgumentCountException };
use ArrayObject, ArrayIterator;
use function arrays\{to_array, to_object, is_digit};

/**
 * @package arrays
 * @object  arrays\AbstractArray
 * @author  Kerem Güneş <k-gun@mail.com>
 */
abstract class AbstractArray extends ArrayObject //implements ArrayInterface
{
    use ArrayTrait;

    protected $type;

    public function __construct(?array $items, string $itemsType)
    {
        $this->type = $itemsType;
        if (Type::isMapKind($this)) {
            $items = to_object($items);
        }
        parent::__construct($items);
    }

    public final function type(): string { return $this->type; }

    public final function size(): int { return $this->count(); }
    public final function toArray(bool $normalize = false): array {
        $ret = $this->getArrayCopy();
        if ($normalize) {
            $digit = false;
            foreach ($ret as $key => $_) {
                if (is_digit($key)) {
                    $digit = true; break; }
            }
            $ret = $digit ? array_values($ret) : $ret;
        }
        return $ret;
    }
    public final function toObject(): object { return (object) $this->toArray(true); }
    public final function toJson(): string { return (string) json_encode($this->toArray()); }
}
