<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Arrays, Type,
    ArrayTrait, ArrayInterface, ArraysException };
use arrays\exception\{
    TypeException, MethodException };
use ArrayIterator;

/**
 * @package arrays
 * @object  arrays\AbstractArray
 * @author  Kerem Güneş <k-gun@mail.com>
 */
abstract class AbstractArray implements ArrayInterface
{
    use ArrayTrait;

    protected $items;
    protected $itemsType;

    static protected $methods = ['search', 'has',
        'hasKey' => '%s(string $key, any $value): bool',
        'hasValue'
    ];

    public function __construct(?array $items, string $itemsType)
    {
        $this->items = $items ?? [];
        $this->itemsType = $itemsType;
    }

    public function __call(string $func, array $funcArgs)
    {
        $method = array_key_exists($func, self::$methods) ? $func : null;
        $methodArgs = $funcArgs;
        if ($method == null) {
            throw new MethodException(sprintf('No method such %s::%s()', static::class, $func));
        }

        $methodArgs0 = $methodArgs[0] ?? null;
        $methodArgs1 = $methodArgs[1] ?? null;

        // let's do this shit..
        switch ($method) {
            case 'search':
                $value = $methodArgs0;
                switch ($this->itemsType) {
                    case Type::INT_MAP:
                        if (!is_int($value)) {
                            $this->throwValueException($value, 'search');
                        } break;
                } break;
            case 'has':
                $value = $methodArgs0;
                switch ($this->itemsType) {
                    case Type::INT_MAP:
                        if (!is_int($value)) {
                            $this->throwValueException($value, 'has');
                        } break;
                } break;
            case 'hasKey':
                $key = $methodArgs0;
                switch ($this->itemsType) {
                    case Type::MAP:
                        if (!is_string($key)) {
                            $this->throwArgumentException('hasKey', 1, 'string', $key);
                        } break;
                }
                break;
            // default:
        }

        // finally call private method that comes from ArrayTrait
        return $this->{'_'.$method}(...$methodArgs);
    }

    public final function setType(int $itemsType): void { $this->itemsType = $itemsType; }
    public final function getType(): string { return $this->itemsType; }

    public final function size(): int { return $this->count(); }
    public final function toArray(): array { return $this->items; }
    public final function toObject(): object { return (object) $this->items; }
    public final function toJson(): string { return (string) json_encode($this->items); }

    /**
     * @inheritDoc \IteratorAggregate
     */
    public final function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    protected final function validateItems(array $items, string $itemsType, string &$error = null): bool
    {
        static $mapMessage = '%ss accept associative arrays with string keys,'.
            ' %s key given (offset: %s)',
               $setMessage = '%ss accept non-associative arrays with unsigned int keys,'.
            ' %s key given (offset: %s)';

        $offset = 0;
        switch ($itemsType) {
            // maps
            case Type::MAP:
                foreach ($items as $key => $value) {
                    if (!is_string($key)) {
                        $error = sprintf($mapMessage, Type::MAP, Type::get($key), $offset);
                            return false; }
                    $offset++;
                } break;
            case Type::INT_MAP:
                foreach ($items as $key => $value) {
                    if (!is_string($key)) {
                        return sprintf($mapMessage, Type::get($key), $offset); }
                    if (!is_int($value)) {
                        return sprintf('IntMaps accept associative arrays with int values,'.
                            ' %s value given (offset: %s)', Type::get($value), $offset); }
                    $offset++;
                } break;
            case Type::FLOAT_MAP:
                foreach ($items as $key => $value) {
                    if (!is_string($key)) {
                        return sprintf($mapMessage, Type::get($key), $offset); }
                    if (!is_float($value)) {
                        return sprintf('FloatMaps accept associative arrays with float values,'.
                            ' %s value given (offset: %s)', Type::get($value), $offset); }
                    $offset++;
                } break;
            case Type::STRING_MAP:
                foreach ($items as $key => $value) {
                    if (!is_string($key)) {
                        return sprintf($mapMessage, Type::get($key), $offset); }
                    if (!is_string($value)) {
                        return sprintf('StringMaps accept associative arrays with string values,'.
                            ' %s value given (offset: %s)', Type::get($value), $offset); }
                    $offset++;
                } break;
            case Type::BOOL_MAP:
                foreach ($items as $key => $value) {
                    if (!is_string($key)) {
                        return sprintf($mapMessage, Type::get($key), $offset); }
                    if (!is_bool($value)) {
                        return sprintf('BoolMaps accept associative arrays with bool values,'.
                            ' %s value given (offset: %s)', Type::get($value), $offset); }
                    $offset++;
                } break;
            // sets
            case Type::SET:
                foreach ($items as $key => $value) {
                    if (!is_int($key)) {
                        return sprintf($setMessage, Type::get($key), $offset); }
                    $offset++;
                } break;
            case Type::INT_SET:
                foreach ($items as $key => $value) {
                    if (!is_int($key) || $key < 0) {
                        return sprintf($setMessage, Type::get($key), $offset); }
                    if (!is_int($value)) {
                        return sprintf('IntSets accept non-associative arrays with int values,'.
                            ' %s value given (offset: %s)', Type::get($value), $offset); }
                    $offset++;
                } break;
            // others
            default:
                $isPrimitiveType = in_array($itemsType, ['int', 'float', 'string', 'bool']);
                foreach ($items as $key => $value) {
                    if ($isPrimitiveType) {
                        if ($itemsType == 'int' && !is_int($value)) {
                            return sprintf('Each item must be type of int, %s given (offset: %s)',
                                Type::get($value), $offset);
                        } elseif ($itemsType == 'float' && !is_float($value)) {
                            return sprintf('Each item must be type of float, %s given (offset: %s)',
                                Type::get($value), $offset);
                        } elseif ($itemsType == 'string' && !is_string($value)) {
                            return sprintf('Each item must be type of string, %s given (offset: %s)',
                                Type::get($value), $offset);
                        } elseif ($itemsType == 'bool' && !is_bool($value)) {
                            return sprintf('Each item must be type of bool, %s given (offset: %s)',
                                Type::get($value), $offset);
                        }
                    } elseif ($itemsType == 'array' && !is_array($value)) {
                        return sprintf('Each item must be type of array, %s given (offset: %s)',
                            Type::get($value), $offset);
                    } elseif ($itemsType == 'object' && !is_object($value)) {
                        return sprintf('Each item must be type of object, %s given (offset: %s)',
                            Type::get($value), $offset);
                    } else {
                        // object type check
                        if (!is_a($value, $itemsType)) {
                            return sprintf('Each item must be type of %s, %s given (offset: %s)',
                                $itemsType, ('object' == $valueType = gettype($value))
                                    ? get_class($value) : Type::get($value), $offset);
                        }
                    }

                    $offset++;
                }
        }

        return true;
    }

    protected final function throwArgumentException(string $method, int $argNum, string $argType, $input): void
    {
        $message = sprintf('Argument %s given to %s() must be %s, %s given', $argNum,
            ($methodPath = static::class .'::'. $method), $argType, Type::get($input));

        $tip = self::$methods[$method] ?? null;
        if ($tip != null) {
            $message .= sprintf(" [tip => {$tip}]", substr($methodPath, strpos($methodPath, '\\') + 1));
        }

        throw new TypeException($message);
    }

    protected final function throwKeyException($key, string $method = null): void
    {
        $message = sprintf('%s keys should be int, %s argument given', $this->itemsType, Type::get($key));
        if ($method != null) {
            $message .= sprintf(' [call to %s::%s(%s)]', $this->itemsType, $method, $this->toCallArgument($key));
        }
        throw new InvalidKeyTypeException($message);
    }
    protected final function throwValueException($value, string $method = null): void
    {
        $message = sprintf('%s values should be int, %s argument given', $this->itemsType, Type::get($value));
        if ($method != null) {
            $message .= sprintf(' [call to %s::%s(%s)]', $this->itemsType, $method, $this->toCallArgument($value));
        }
        throw new InvalidValueTypeException($message);
    }

    protected final function toCallArgument($argument): string
    {
        if (is_null($argument)) return 'null';
        if (is_bool($argument)) return $argument ? 'true' : 'false';
        if (is_string($argument)) return "'{$argument}'";
        if (is_array($argument)) return 'Array[...]';
        if (is_object($argument)) return 'Object(...)';
        return (string) $argument;
    }
}
