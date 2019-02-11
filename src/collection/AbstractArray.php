<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2019 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

namespace objects\collection;

use objects\{Type, TheObject};
use objects\util\ArrayUtil;
use objects\exception\{TypeException, MethodException, ArgumentException, ArgumentTypeException,
    MutationException, NullException};
use objects\collection\CollectionException;
use Countable, IteratorAggregate, ArrayObject, Generator, Closure;

/**
 * @package objects\collection
 * @object  objects\collection\AbstractArray
 * @author  Kerem Güneş <k-gun@mail.com>
 */
abstract class AbstractArray extends TheObject implements ArrayInterface, Countable, IteratorAggregate
{
    /**
     * ArrayTrait.
     * @object objects\collection\ArrayTrait
     */
    use ArrayTrait;

    /**
     * Stack.
     * @var ArrayObject
     */
    private $stack;

    /**
     * Methods.
     * @var array
     */
    private static $methods = [];

    /**
     * Not allowed methods.
     * @var array
     */
    protected static $notAllowedMethods = [];

    /**
     * Constructor.
     * @param string     $type
     * @param array|null $items
     */
    public function __construct(string $type, array $items = null)
    {
        $items = $items ?? [];
        if (Type::isMapLike($this)) {
            $items = Type::toObject($items);
        }

        $this->stack = new ArrayObject($items);
    }

    /**
     * Call magic.
     * @param  string $method
     * @param  array  $methodArgs
     * @return any
     * @throws objects\exception\MethodException
     */
    public final function __call(string $method, array $methodArgs)
    {
        $this->methodCheck($method);

        if (!isset(self::$methods[$method])) {
            throw new MethodException("Method {$this->getShortName()}::{$method}() does not exist", 1);
        }

        return self::$methods[$method](...$methodArgs);
    }

    /**
     * Prototype (adds new methods to send call magic).
     * @param  string   $method
     * @param  callable $methodFunc
     * @return void
     * @throws objects\exception\MethodException
     */
    public final function prototype(string $method, callable $methodFunc): void
    {
        if (in_array($method, self::$notAllowedMethods)) {
            throw new MethodException("Method {$method}() not allowed for {$this->getShortName()}() objects");
        }
        if (method_exists($this, $method)) {
            throw new MethodException("Method {$this->getShortName()}::{$method}() already exists", 2);
        }

        if ($methodFunc instanceof Closure) {
            $methodFunc = $methodFunc->bindTo($this);
        }

        self::$methods[$method] = $methodFunc;
    }

    /**
     * Item.
     * @param  int|string $key
     * @return any|null
     */
    public final function item($key)
    {
        $this->keyCheck($key);

        return $this->stack[$key] ?? null;
    }

    /**
     * Items.
     * @param  bool $pair
     * @return array
     */
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

    /**
     * Reset.
     * @param  array $items
     * @return self
     * @throws objects\exception\MutationException
     */
    public final function reset(array $items): self
    {
        $this->methodCheck('reset');
        $this->readOnlyCheck();

        $this->stack->exchangeArray($items);

        return $this;
    }

    /**
     * Reset items (alias of reset()).
     * @param  array $items
     * @return self
     * @throws objects\exception\MutationException
     */
    public final function resetItems(array $items): self
    {
        $this->methodCheck('resetItems');

        return $this->reset($items);
    }

    /**
     * Copy.
     * @return objects\collection\ArrayInterface
     */
    public final function copy(): ArrayInterface
    {
        return clone $this;
    }

    /**
     * Copy array.
     * @return array
     */
    public final function copyArray(): array
    {
        return $this->stack->getArrayCopy();
    }

    /**
     * Size.
     * @return int
     */
    public final function size(): int
    {
        return $this->stack->count();
    }

    /**
     * Empty.
     * @return void.
     */
    public final function empty(): void
    {
        $this->methodCheck('empty');
        $this->reset([]);
    }

    /**
     * Is empty.
     * @return bool
     */
    public final function isEmpty(): bool
    {
        return $this->stack->count() == 0;
    }

    /**
     * Count.
     * @return int
     */
    public final function count(): int
    {
        return $this->stack->count();
    }

    /**
     * Count values.
     * @return array
     */
    public final function countValues(): array
    {
        return array_count_values($this->items());
    }

    /**
     * Keys.
     * @return array
     */
    public final function keys(): array
    {
        return array_keys($this->items());
    }

    /**
     * Values.
     * @return array
     */
    public final function values(): array
    {
        return array_values($this->items());
    }

    /**
     * First.
     * @return any|null
     */
    public final function first()
    {
        return $this->values()[0] ?? null;
    }

    /**
     * First key.
     * @return int|string|null
     */
    public final function firstKey()
    {
        return $this->keys()[0] ?? null;
    }

    /**
     * Last.
     * @return any|null
     */
    public final function last()
    {
        return $this->values()[$this->size() - 1] ?? null;
    }

    /**
     * Last key.
     * @return int|string|null
     */
    public final function lastKey()
    {
        return $this->keys()[$this->size() - 1] ?? null;
    }

    /**
     * To array.
     * @param  bool $normalize
     * @return array
     */
    public final function toArray(bool $normalize = false): array
    {
        $ret = $this->stack->getArrayCopy();

        // i do not remember why is this here..
        if ($normalize) {
            $allKeysDigit = null;
            foreach ($ret as $key => $_) {
                if (!Type::isDigit($key)) { $allKeysDigit = false; break; }
            }
            $ret = $allKeysDigit ? array_values($ret) : $ret;
        }

        return $ret;
    }

    /**
     * To object.
     * @return object
     */
    public final function toObject(): object
    {
        return (object) $this->toArray();
    }

    /**
     * To json.
     * @return string
     */
    public final function toJson(): string
    {
        return (string) json_encode($this->toArray());
    }

    /**
     * Map.
     * @param  callable $func
     * @param  bool     $breakable
     * @return self
     * @throws objects\exception\MutationException
     */
    public final function map(callable $func): self
    {
        $this->methodCheck('map');
        $this->readOnlyCheck();

        return $this->reset(array_map($func, $this->items()));
    }

    /**
     * Reduce.
     * @param  any           $initialValue
     * @param  callable|null $func
     * @return any
     */
    public final function reduce($initialValue = null, callable $func = null)
    {
        // set func to sum as default
        $func = $func ?? function ($initialValue, $value) {
            return is_numeric($value) ? $initialValue += $value : $initialValue;
        };

        return array_reduce($this->values(), $func, $initialValue);
    }

    /**
     * Filter.
     * @param  callable|null $func
     * @return self
     * @throws objects\exception\MutationException
     */
    public final function filter(callable $func = null): self
    {
        $this->methodCheck('filter');
        $this->readOnlyCheck();

        // set func to empty check as default
        $func = $func ?? function ($value) {
            return strlen((string) $value);
        };

        return $this->reset(array_filter($this->items(), $func, 2));
    }

    /**
     * Diff.
     * @param  iterable $stack2
     * @param  bool     $uniq
     * @return array
     */
    public final function diff(iterable $stack2, bool $uniq = false): array
    {
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

    /**
     * Uniq.
     * @return array
     */
    public final function uniq(): array
    {
        return array_unique($this->items());
    }

    /**
     * Ununiq.
     * @return array
     */
    public final function ununiq(): array
    {
        $items = $this->items();
        return array_filter($items, function ($value, $key) use ($items) {
            return array_search($value, $items) !== $key;
        }, 1);
    }

    /**
     * Uniqs.
     * @return array
     */
    public final function uniqs(): array
    {
        return array_diff($this->uniq(), $this->ununiq());
    }

    /**
     * Merge.
     * @param  self $array
     * @return self
     * @throws objects\exception\MutationException,ArgumentException
     */
    public final function merge(self $array): self
    {
        $this->methodCheck('merge');
        $this->readOnlyCheck();

        if ($array->type() != $this->type) {
            throw new ArgumentException("Given {$array->getName()} not mergable with {$this->getName()}");
        }

        return $this->reset(array_merge($this->items(), $array->items()));
    }

    /**
     * Chunk.
     * @param  int    $size
     * @param  bool   $preserveKeys
     * @return array
     */
    public final function chunk(int $size, bool $preserveKeys = false): array
    {
        return array_chunk($this->items(), $size, $preserveKeys);
    }

    /**
     * Slice.
     * @param  int      $offset
     * @param  int|null $size
     * @param  bool     $preserveKeys
     * @return array
     */
    public final function slice(int $offset, int $size = null, bool $preserveKeys = false): array
    {
        return array_slice($this->items(), $offset, $size, $preserveKeys);
    }

    /**
     * Reverse.
     * @return self
     * @throws objects\exception\MutationException
     */
    public final function reverse(): self
    {
        $this->methodCheck('reverse');
        $this->readOnlyCheck();

        return $this->reset(array_reverse($this->items()));
    }

    /**
     * Rand.
     * @param  int  $size
     * @param  bool $useKeys
     * @return any
     */
    public final function rand(int $size = 1, bool $useKeys = false)
    {
        return ArrayUtil::rand($this->items(), $size, $useKeys);
    }

    /**
     * Shuffle.
     * @param  bool|null $preserveKeys
     * @return self
     * @throws objects\exception\MutationException
     */
    public final function shuffle(bool $preserveKeys = null): self
    {
        $this->methodCheck('shuffle');
        $this->readOnlyCheck();

        $items = $this->items();
        if ($items != null) {
            ArrayUtil::shuffle($items, $preserveKeys ?? Type::isMapLike($this));
        }

        return $this->reset($items);
    }

    /**
     * Sort.
     * @param  callable|null $func
     * @param  callable|null $ufunc
     * @param  int           $flags
     * @return self
     * @throws objects\exception\MutationException
     */
    public final function sort(callable $func = null, callable $ufunc = null, int $flags = 0): self
    {
        $this->readOnlyCheck();

        $items = $this->items();
        return $this->reset(ArrayUtil::sort($items, $func, $ufunc, $flags));
    }

    /**
     * Sort key.
     * @param  callable|null $ufunc
     * @param  int           $flags
     * @return self
     */
    public final function sortKey(callable $ufunc = null, int $flags = 0): self
    {
        return $this->sort('ksort', $ufunc, $flags);
    }

    /**
     * Sort natural.
     * @param  bool   $caseSensitive
     * @param  int    $flags
     * @return self
     */
    public final function sortNatural(bool $caseSensitive = true, int $flags = 0): self
    {
        $flags += SORT_NATURAL;
        if (!$caseSensitive) {
            $flags += SORT_FLAG_CASE;
        }

        return $this->sort(null, null, $flags);
    }

    /**
     * Sort locale.
     * @param  string        $locale
     * @param  callable|null $func
     * @param  callable|null $ufunc
     * @param  int           $flags
     * @return self
     */
    public final function sortLocale(string $locale, callable $func = null, callable $ufunc = null,
        int $flags = 0): self
    {
        $localeDefault = setlocale(LC_COLLATE, '');
        setlocale(LC_COLLATE, $locale);
        $this->sort($func, $ufunc, $flags += SORT_LOCALE_STRING);
        setlocale(LC_COLLATE, $localeDefault); // reset locale

        return $this;
    }

    /**
     * Test.
     * @param  Closure $func
     * @return bool
     */
    public final function test(Closure $func): bool
    {
        return ArrayUtil::test($this->items(), $func);
    }

    /**
     * Test all.
     * @param  Closure $func
     * @return bool
     */
    public final function testAll(Closure $func): bool
    {
        return ArrayUtil::testAll($this->items(), $func);
    }

    /**
     * Key check.
     * @param  int|string $key
     * @return void
     * @throws array\ArgumentTypeException
     */
    public final function keyCheck($key): void
    {
        if ($message = ArrayUtil::keyCheck($key, false)) {
            throw new ArgumentTypeException($message);
        }
    }

    /**
     * Null check.
     * @param  any $value
     * @return void
     * @throws array\NullException
     */
    public final function nullCheck($value): void
    {
        if (!$this->allowNulls && $value === null) {
            throw new NullException("{$this->getShortName()}() object do not accept null values,".
                " null given");
        }
    }

    /**
     * Method check.
     * @param  string $method
     * @return void
     * @throws objects\exception\MethodException
     */
    public final function methodCheck(string $method): void
    {
        if (self::$notAllowedMethods && in_array($method, self::$notAllowedMethods)) {
            throw new MethodException("Method {$method}() not allowed for {$this->getShortName()}() objects");
        }
    }

    /**
     * Read only check.
     * @return void
     * @throws objects\exception\MutationException
     */
    public final function readOnlyCheck(): void
    {
        if ($this->readOnly) {
            $method =@ end(debug_backtrace(0))['function'];
            throw new MutationException("Cannot modify read-only {$this->getShortName()}() object".
                " [called method: {$method}()]");
        }
    }

    /**
     * Generate.
     * @param  bool $reverse
     * @return Generator
     */
    public final function generate(bool $reverse = false): Generator
    {
        if (!$reverse) {
            foreach ($this->stack as $key => $value) {
                yield $key => $value;
            }
        } else {
            $stack = $this->stack;
            for (end($stack); (null !== $key = key($stack)); prev($stack)) {
                yield $key => current($stack);
            }
        }
    }

    /**
     * Get iterator.
     * @return Generator
     */
    public final function getIterator(): Generator
    {
        return $this->generate();
    }

    /**
     * Min.
     * @param  bool $numericsOnly
     * @return any|null
     */
    public final function min(bool $numericsOnly = false)
    {
        $values = $this->values();
        if ($numericsOnly) {
            $values = array_filter($values, 'is_numeric');
        }

        return (false !== $ret =@ min($values)) ? $ret : null;
    }

    /**
     * Max.
     * @param  bool $numericsOnly
     * @return any|null
     */
    public final function max(bool $numericsOnly = false)
    {
        $values = $this->values();
        if ($numericsOnly) {
            $values = array_filter($values, 'is_numeric');
        }

        return (false !== $ret =@ max($values)) ? $ret : null;
    }

    /**
     * Calc.
     * @param  string    $operator
     * @param  bool      $numericsOnly
     * @param  int|null &$valueCount
     * @return number|null
     * @throws objects\collection\CollectionException
     */
    public final function calc(string $operator, bool $numericsOnly = true, int &$valueCount = null)
    {
        $values = $this->values();
        if ($numericsOnly) {
            $values = array_filter($values, 'is_numeric');
        }

        if ($values != null) {
            $result = array_shift($values);
            $valueCount = 1;
            foreach ($values as $value) {
                switch ($operator) {
                    case '+': $result += $value; break;
                    case '-': $result -= $value; break;
                    case '/': $result /= $value; break;
                    case '*': $result *= $value; break;
                    case '**': $result **= $value; break;
                    default: throw new CollectionException("Unknown operator {$operator} given");
                }
                $valueCount++;
            }
        }

        return $result ?? null;
    }

    /**
     * Calc avg.
     * @param  string    $operator
     * @param  bool      $numericsOnly
     * @param  int|null &$valueCount
     * @return ?float
     */
    public final function calcAvg(string $operator, bool $numericsOnly = true, int &$valueCount = null): ?float
    {
        $result = $this->calc($operator, $numericsOnly, $valueCount);

        return $valueCount ? $result / $valueCount : null;
    }

    /**
     * Sum.
     * @param  bool      $numericsOnly
     * @param  int|null &$valueCount
     * @return number|null
     */
    public final function sum(bool $numericsOnly = true, int &$valueCount = null)
    {
        return $this->calc('+', $numericsOnly, $valueCount);
    }

    /**
     * Sum avg.
     * @param  bool      $numericsOnly
     * @param  int|null &$valueCount
     * @return ?float
     */
    public final function sumAvg(bool $numericsOnly = true, int &$valueCount = null): ?float
    {
        return $this->calcAvg('+', $numericsOnly, $valueCount);
    }

    /**
     * Stack command.
     * @param  string     $command
     * @param  any    &...$arguments
     * @return void
     * @throws objects\exception\MutationException,NullException
     * @throws objects\collection\CollectionException
     */
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
                throw new CollectionException("Unknown command {$command}");
        }
    }
}
