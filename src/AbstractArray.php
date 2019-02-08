<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Arrays, Type,
    ArrayTrait, ArrayInterface, ArraysException };
use arrays\exception\{
    TypeException, MethodException,
    ArgumentException, ArgumentCountException };
use ArrayObject;

/**
 * @package arrays
 * @object  arrays\AbstractArray
 * @author  Kerem Güneş <k-gun@mail.com>
 */
abstract class AbstractArray extends ArrayObject implements ArrayInterface
{
    use ArrayTrait;

    public function __construct(string $type, array $items = null)
    {
        if (Type::isMapLike($this)) {
            $items = Type::toObject($items);
        }
        parent::__construct($items);
    }

    public final function keys(): array { return array_keys($this->toArray()); }
    public final function values(): array { return array_values($this->toArray()); }

    public final function size(): int { return $this->count(); }
    public final function toArray(bool $normalize = false): array {
        $ret = $this->getArrayCopy();
        if ($normalize) {
            $digit = false;
            foreach ($ret as $key => $_) {
                if (Type::isDigit($key)) {
                    $digit = true; break; }
            }
            $ret = $digit ? array_values($ret) : $ret;
        }
        return $ret;
    }
    public final function toObject(): object { return (object) $this->toArray(true); }
    public final function toJson(): string { return (string) json_encode($this->toArray()); }

    public final function getName(): string
    {
        return static::class;
    }
    public final function getShortName(): string
    {
        return substr($name = $this->getName(),
            (false !== $nssPos = strpos($name, '\\')) ? $nssPos + 1 : 0);
    }
}
