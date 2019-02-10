<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Arrays, Type,
    AnyArray,
    ArrayTrait, ArrayInterface };
use arrays\exception\{
    ArrayException, TypeException,
    ArgumentException, MethodException };
use Countable, IteratorAggregate, ArrayObject, Generator, Closure;

/**
 * @package arrays
 * @object  arrays\AbstractArray
 * @author  Kerem Güneş <k-gun@mail.com>
 */
abstract class AbstractArray implements ArrayInterface, Countable, IteratorAggregate
{
    use ArrayTrait;

    private $stack;
    private static $methods = [];
    protected static $notAllowedMethods = [];

    public function __construct(string $type, array $items = null)
    {
        $items = $items ?? [];
        if (Type::isMapLike($this)) {
            $items = Type::toObject($items);
        }
        $this->stack = new ArrayObject($items);
    }

    public final function __call($method, $methodArgs) {
        if (in_array($method, self::$notAllowedMethods)) {
            throw new MethodException("Method {$method}() not allowed for {$this->getShortName()}() objects");
        }
        if (!isset(self::$methods[$method])) {
            throw new MethodException("Method {$this->getShortName()}::{$method}() does not exist");
        }
        return self::$methods[$method](...$methodArgs);
    }

    public final function prototype(string $method, callable $methodFunc): void {
        if (in_array($method, self::$notAllowedMethods)) {
            throw new MethodException("Method {$method}() not allowed for {$this->getShortName()}() objects");
        }
        if (method_exists($this, $method)) {
            throw new MethodException("Method {$this->getShortName()}::{$method}() already exists");
        }
        if ($methodFunc instanceof Closure) {
            $methodFunc = $methodFunc->bindTo($this);
        }
        self::$methods[$method] = $methodFunc;
    }

    public final function item($key) { return $this->stack[$key] ?? null; }
    public final function items(bool $pair = false): array {
        $ret = $this->stack->getArrayCopy();
        if ($pair) {
            $retTmp = [];
            foreach ($ret as $key => $value) {
                $retTmp[] = [$key, $value];
            }
            $ret = $retTmp;
        }
        return $ret;
    }

    public final function copy() { return clone $this; }
    public final function copyArray() { return $this->stack->getArrayCopy(); }

    public final function size(): int { return $this->stack->count(); }
    public final function empty(): void { $this->reset([]); }
    public final function isEmpty(): bool { return !$this->stack->count(); }
    public final function reset(array $items): self {
        $this->readOnlyCheck();
        $this->stack->exchangeArray($items);
        return $this;
    }

    public final function count(): int { return $this->stack->count(); }
    public final function countValues(): array { return array_count_values($this->items()); }

    public final function keys(): array { return array_keys($this->items()); }
    public final function values(): array { return array_values($this->items()); }

    public final function first() { return $this->values()[0] ?? null; }
    public final function firstKey() { return $this->keys()[0] ?? null; }
    public final function last() { return $this->values()[$this->size() - 1] ?? null; }
    public final function lastKey() { return $this->keys()[$this->size() - 1] ?? null; }

    public final function toArray(bool $normalize = false): array {
        $ret = $this->stack->getArrayCopy();
        // i do not remember why is this here..
        // if ($normalize) {
        //     $allKeysDigit = null;
        //     foreach ($ret as $key => $_) {
        //         if (!Type::isDigit($key)) { $allKeysDigit = false; break; }
        //     }
        //     $ret = $allKeysDigit ? array_values($ret) : $ret;
        // }
        return $ret;
    }
    public final function toObject(): object { return (object) $this->toArray(true); }
    public final function toJson(): string { return (string) json_encode($this->toArray(true)); }

    public final function map(callable $func, bool $breakable = false): self {
        $this->readOnlyCheck();
        return $this->reset(array_map($func, $this->items()));
    }
    public final function reduce($initialValue = null, callable $func = null) {
        // set sum as default
        $func = $func ?? function ($initialValue, $value) {return is_numeric($value) ? $initialValue += $value : $initialValue;};
        return array_reduce($this->values(), $func, $initialValue);
    }
    public final function filter(callable $func = null): self {
        $this->readOnlyCheck();
        // set empty filter as default
        $func = $func ?? function ($value) { return strlen((string) $value); };
        return $this->reset(array_filter($this->items(), $func, 2));
    }

    public final function diff(iterable $stack2, bool $uniq = false): array {
        $stack1 = $this->items();
        if ($stack2 instanceof Traversable) {
            iterator_to_array($stack2);
        }
        if ($uniq) {
            $stack1 = array_unique($stack1);
            $stack2 = array_unique($stack2);
        }
        return array_diff($stack1, $stack2);
    }

    public final function uniq(): array { return array_unique($this->items()); }
    public final function ununiq(): array {
        $items = $this->items();
        return array_filter($items, function ($value, $key) use ($items) {
            return array_search($value, $items) !== $key;
        }, 1);
    }
    public final function uniqs(): array { return array_diff($this->uniq(), $this->ununiq()); }

    public final function merge(self $array): self {
        $this->readOnlyCheck();
        if ($array->type() != $this->type) {
            throw new ArgumentException("Given {$array->getName()} not mergable with {$this->getName()}");
        }
        return $this->reset(array_merge($this->items(), $array->items()));
    }
    public final function chunk(int $size, bool $preserveKeys = false): array {
        return array_chunk($this->items(), $size, $preserveKeys);
    }
    public final function slice(int $offset, int $size = null, bool $preserveKeys = false): array {
        return array_slice($this->items(), $offset, $size, $preserveKeys);
    }

    public final function rand(int $size = 1, bool $useKeys = false) {
        if ($size < 1) {
            throw new ArgumentException(sprintf('Minimum size could be 1 for %s(), %s given',
                $this->getMethodName(), $size));
        }
        $items = $this->items();
        if ($items != null) {
            $keys = array_keys($items);
            shuffle($keys);
            while ($size--) {
                $value = $items[$keys[$size]] ?? null;
                if (!$useKeys) {
                    $ret[] = $value;
                } else {
                    $ret[$keys[$size]] = $value;
                }
            }
            if (count($ret) == 1) {
                $ret = !$useKeys ? current($ret) : [key($ret), current($ret)];
            }
        }
        return $ret ?? null;
    }

    public final function shuffle(bool $preserveKeys = null): self {
        $this->readOnlyCheck();
        $items = $this->items();
        if ($items != null) {
            $preserveKeys = $preserveKeys ?? Type::isMapLike($this);
            if ($preserveKeys) {
                $keys = array_keys($items);
                shuffle($keys);
                $values = $this->items();
                $shuffledItems = [];
                foreach ($keys as $key) {
                    $shuffledItems[$key] = $items[$key];
                }
                $items = $shuffledItems;
            } else {
                shuffle($items);
            }
            // nope.. (cos' killing speed and also randomness)
            // uasort($items, function () {
            //     return rand(-1, 1);
            // });
        }
        return $this->reset($items);
    }
    public final function reverse(): self {
        $this->readOnlyCheck();
        return $this->reset(array_reverse($this->items()));
    }

    public final function sort(callable $func = null, callable $ufunc = null, int $flags = 0): self {
        $this->readOnlyCheck();
        $items = $this->items();
        return $this->reset(Arrays::sort($items, $func, $ufunc, $flags));
    }
    public final function sortKey(callable $ufunc = null, int $flags = 0): self {
        return $this->sort('ksort', $ufunc, $flags);
    }
    public final function sortNatural(bool $caseSensitive = true, int $flags = 0): self {
        $flags += SORT_NATURAL;
        if (!$caseSensitive) {
            $flags += SORT_FLAG_CASE;
        }
        return $this->sort(null, null, $flags);
    }
    public final function sortLocale(string $locale, callable $func = null, callable $ufunc = null, int $flags = 0): self {
        $localeDefault = setlocale(LC_COLLATE, '');
        setlocale(LC_COLLATE, $locale);
        $this->sort($func, $ufunc, $flags += SORT_LOCALE_STRING);
        setlocale(LC_COLLATE, $localeDefault); // reset locale
        return $this;
    }

    public final function test(Closure $func): bool { return Arrays::test($this->toArray(), $func); }
    public final function testAll(Closure $func): bool { return Arrays::testAll($this->toArray(), $func); }

    public final function getName(): string { return static::class; }
    public final function getShortName(): string {
        return substr($name = $this->getName(),
            (false !== $nssPos = strpos($name, '\\')) ? $nssPos + 1 : 0);
    }
    public final function getMethodName(string $method = null): string {
        return sprintf('%s::%s', $this->getName(), $method ?? debug_backtrace()[1]['function']);
    }

    public final function nullCheck($value): void {
        if ($value === null && !$this->allowNulls) {
            throw new ArgumentException("{$this->getShortName()}() object do not accept null values, null given");
        }
    }
    public final function readOnlyCheck(): void {
        if ($this->readOnly) {
            throw new ArrayException("Cannot modify read-only {$this->getShortName()}() object");
        }
    }

    public final function generate(): Generator
    {
        foreach ($this->stack as $key => $value) {
            yield $key => $value;
        }
    }
    public final function generateReverse(): Generator
    {
        $stack = $this->stack;
        for (end($stack); (null !== $key = key($stack)); prev($stack)) {
            yield $key => current($stack);
        }
    }
    public final function getIterator(bool $reverse = true): Generator
    {
        return $this->generate($this->stack);
    }

    // some math..

    // @return number
    public final function min() { return (false !== $ret =@ min(array_filter($this->values(), 'is_numeric'))) ? $ret : null; }
    public final function max() { return (false !== $ret =@ max(array_filter($this->values(), 'is_numeric'))) ? $ret : null; }
    public final function calc(string $operator, bool $strict = false, int &$valueCount = null)
    {
        $values = $this->values();
        $ret = array_shift($values);
        if ($ret === null) return null;

        $valueCount = 1;
        foreach ($values as $value) {
            if (!is_numeric($value)) {
                if ($strict) {
                    $value = Type::export($value);
                    throw new ArrayException("A non-numeric value {$value} encountered");
                }
                continue;
            }
            switch ($operator) {
                case '+': $ret += $value; break;
                case '-': $ret -= $value; break;
                case '/': $ret /= $value; break;
                case '*': $ret *= $value; break;
                case '**': $ret **= $value; break;
                default: throw new ArrayException("Unknown operator {$operator} given");
            }
            $valueCount++;
        }
        return $ret;
    }
    public function calcAvg(string $operator, bool $strict = false, int &$valueCount = null): ?float {
        return ($calc = $this->calc($operator, $strict, $valueCount)) && $valueCount ? $calc / $valueCount : null;
    }
    public function sum(bool $strict = false, int &$valueCount = null) { return $this->calc('+', $strict, $valueCount); }
    public function sumAvg(bool $strict = false, int &$valueCount = null): ?float { return $this->calcAvg('+', $strict, $valueCount); }

    // ---

    private final function stackCommand(string $command, &...$arguments): void
    {
        switch ($command) {
            case 'set':
                $this->readOnlyCheck();
                [$key, $value] = $arguments;
                $this->nullCheck($value);
                $this->stack->offsetSet($key, $value);
                $arguments[2] = $this->stack->count();
                break;
            case 'get':
                $key = $arguments[0];
                $arguments[1] =@ $this->stack->offsetGet($key);
                break;
            case 'unset':
                $this->readOnlyCheck();
                $key = $arguments[0];
                @ $this->stack->offsetUnset($key);
                break;
            case 'pop':
            case 'shift':
                $this->readOnlyCheck();
                $key = ($command == 'pop') ? $this->lastKey() : $this->firstKey();
                $arguments[0] =@ $this->stack->offsetGet($key);
                @ $this->stack->offsetUnset($key);
                $arguments[1] = $this->stack->count();
                break;
            case 'unpop':
                $this->readOnlyCheck();
                $value = $arguments[0];
                $this->nullCheck($value);
                $this->stack->offsetSet(null, $value);
                $arguments[1] = $this->stack->count();
                break;
            case 'unshift':
                $this->readOnlyCheck();
                $value = $arguments[0];
                $this->nullCheck($value);
                $items = array_merge([$value], $this->items());
                $this->stack->exchangeArray($items);
                $arguments[1] = $this->stack->count();
                break;
            case 'put':
                $this->readOnlyCheck();
                [$key, $value] = $arguments;
                $this->nullCheck($value);
                $this->stack->offsetSet($key, $value);
                break;
            case 'push':
                $this->readOnlyCheck();
                [$key, $value] = $arguments;
                $this->nullCheck($value);
                @ $this->stack->offsetUnset($key);
                $this->stack->offsetSet($key, $value);
                break;
            case 'pull':
                $this->readOnlyCheck();
                $key = $arguments[0];
                $arguments[1] =@ $this->stack->offsetGet($key);
                @ $this->stack->offsetUnset($key);
                break;
            default:
                throw new ArrayException("Unknown command {$command}");
        }
    }
}
