<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Arrays, Type,
    AnyArray,
    ArrayTrait, ArrayInterface };
use arrays\exception\{
    ArrayException, TypeException, ArgumentTypeException };
use ArrayObject;

/**
 * @package arrays
 * @object  arrays\AbstractArray
 * @author  Kerem Güneş <k-gun@mail.com>
 */
abstract class AbstractArray implements ArrayInterface
{
    use ArrayTrait;

    private $stack;

    public function __construct(string $type, array $items = null)
    {
        if (Type::isMapLike($this)) {
            $items = Type::toObject($items);
        }
        $this->stack = new ArrayObject($items);
    }

    public final function item($key) { return $this->stack[$key] ?? null; }
    public final function items(bool $simple = true): array {
        $ret = $this->stack->getArrayCopy();
        if (!$simple) {
            $retTmp = [];
            foreach ($ret as $key => $value) {
                $retTmp[] = [$key, $value];
            }
            $ret = $retTmp;
        }
        return $ret;
    }

    public final function size(): int { return $this->stack->count(); }
    public final function empty(): void { $this->stack->exchangeArray([]); }
    public final function isEmpty(): bool { return !$this->stack->count(); }

    public final function keys(): array { return array_keys($this->stack->getArrayCopy()); }
    public final function values(): array { return array_values($this->stack->getArrayCopy()); }

    public final function first(): array { return $this->values()[0] ?? null; }
    public final function firstKey() { return $this->keys()[0] ?? null; }
    public final function last(): array { return $this->values()[$this->size() - 1] ?? null; }
    public final function lastKey() { return $this->keys()[$this->size() - 1] ?? null; }

    public final function toArray(bool $normalize = false): array {
        $ret = $this->stack->getArrayCopy();
        if ($normalize) {
            // $allKeysDigit = null;
            // foreach ($ret as $key => $_) {
            //     if (!Type::isDigit($key)) {
            //         $allKeysDigit = false;
            //         break;
            //     }
            // }
            // $ret = $allKeysDigit ? array_values($ret) : $ret;
        }
        return $ret;
    }
    public final function toObject(): object { return (object) $this->toArray(true); }
    public final function toJson(): string { return (string) json_encode($this->toArray(true)); }

    public final function getName(): string { return static::class; }
    public final function getShortName(): string {
        return substr($name = $this->getName(),
            (false !== $nssPos = strpos($name, '\\')) ? $nssPos + 1 : 0);
    }
    public final function toString(): string { return sprintf('Object(%s#%s)', $this->getName(), spl_object_id($this)); }

    public final function isMapLike(): bool { return Type::isMapLike($this); }
    public final function isSetLike(): bool { return Type::isSetLike($this); }

    public final function nullCheck($value): void {
        if ($value === null && !$this->allowNulls) {
            throw new ArgumentTypeException(sprintf('%s() object do not accept null values, null given',
                $this->getShortName()));
        }
    }

    public final function readOnlyCheck(): void {
        if ($this->readOnly) {
            throw new ArrayException("Cannot modify read-only {$this->getShortName()}() object");
        }
    }

    private final function stackCommand(string $command, &...$arguments): void
    {
        $this->readOnlyCheck();

        switch ($command) {
            case 'set':
                [$key, $value] = $arguments;
                $this->nullCheck($value);
                $this->stack->offsetSet($key, $value);
                $arguments[2] = $this->stack->count();
                break;
            case 'get':
                $arguments[1] =@ $this->stack->offsetGet($arguments[0]);
                break;
            case 'unset':
                $this->stack->offsetUnset($arguments[0]);
                break;
            case 'pop':
            case 'shift':
                $key = ($command == 'pop') ? $this->lastKey() : $this->firstKey();
                $arguments[0] =@ $this->stack->offsetGet($key);
                @ $this->stack->offsetUnset($key);
                $arguments[1] = $this->stack->count();
                break;
            case 'unpop':
                $this->stack->offsetSet(null, $arguments[0]);
                $arguments[1] = $this->stack->count();
                break;
            case 'unshift':
                $items = array_merge([$arguments[0]], $this->stack->getArrayCopy());
                $this->stack->exchangeArray($items);
                $arguments[1] = $this->stack->count();
                break;
            case 'put':
                [$key, $value] = $arguments;
                $this->nullCheck($value);
                $this->stack->offsetSet($key, $value);
                break;
            case 'push':
                [$key, $value] = $arguments;
                $this->nullCheck($value);
                @ $this->stack->offsetUnset($key);
                $this->stack->offsetSet($key, $value);
                break;
            case 'pull':
                $arguments[1] =@ $this->stack->offsetGet($key = $arguments[0]);
                @ $this->stack->offsetUnset($key);
                break;
            default:
                throw new ArrayException("Unknown command {$command}");
        }
    }
}
