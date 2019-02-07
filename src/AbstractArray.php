<?php
declare(strict_types=1);

namespace arrays;

use arrays\{
    Arrays, Type,
    ArrayTrait, ArrayInterface, ArraysException };
use arrays\exception\{
    TypeException, MethodException,
    ArgumentException, ArgumentCountException };
use ArrayIterator;
use function arrays\is_digit;

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

    private static $onBeforeCheckExists;
    private static $onAfterCheckExists;

    static protected $methods = [
        'search',
        'has', 'hasKey', 'hasValue',
        'set', 'get',
        'add', 'remove', 'removeAt',
    ];

    public function __construct(?array $items, string $itemsType)
    {
        $this->items = $items ?? [];
        $this->itemsType = $itemsType;

        self::$onBeforeCheckExists = method_exists($this, 'onBeforeCheck');
        self::$onAfterCheckExists = method_exists($this, 'onAfterCheck');
    }

    public function __call(string $func, array $funcArgs)
    {
        $method = in_array($func, self::$methods) ? $func : null;
        $methodArgs = $funcArgs;
        if ($method == null) {
            throw new MethodException(sprintf('No method such %s::%s()', static::class, $func));
        }

        // user method (that can override $methodArgs)
        if (self::$onBeforeCheckExists) {
            $this->onBeforeCheck($method, $methodArgs);
        }

        $argsv = $methodArgs;
        $argsc = count($argsv);
        if ($argsc < 2) {
            $argsv = array_pad($argsv, 2, null);
        }

        // let's get that shit done..
        switch ($method) {
            case 'search':
            case 'has':
            case 'hasKey':
                $key = $argsv[0];
                switch ($this->itemsType) {
                    case Type::MAP:
                        if (!Type::validateMapKey($key, $error)) {
                            self::throwArgumentException('hasKey', 1, $error, $key);
                        } break;
                }
                break;
            case 'hasValue':
                break;
            case 'set':
            case 'get':
                $key = $argsv[0]; $value = $argsv[1];
                if ($method == 'set') {
                    if ($argsc < 2) {
                        self::throwArgumentCountException($method, $argsc, 2); }
                    switch ($this->itemsType) {
                        case Type::MAP:
                            if (!Type::validateMapKey($key, $error)) {
                                self::throwArgumentException($method, 1, $error, $key);
                            } break;
                    }
                } elseif ($method == 'get') {
                    if ($argsc < 1) {
                        self::throwArgumentCountException($method, $argsc, 1); }
                    switch ($this->itemsType) {
                        case Type::MAP:
                            if (!Type::validateMapKey($key, $error)) {
                                self::throwArgumentException($method, 1, $error, $key);
                            } break;
                    }
                }
                break;
            case 'add':
                switch ($this->itemsType) {
                    case Type::MAP:
                        throw new MethodException('Cannot call add() method for Maps, use Map::set() instead');
                }
                break;
            case 'remove':
                break;
            case 'removeAt':
                break;
            // default:
        }

        // user method (that can override $methodArgs)
        if (self::$onAfterCheckExists) {
            $this->onAfterCheck($method, $methodArgs);
        }

// function valRef(&$arr) {
//     $refs = array();
//     foreach ($arr as $key => $value) {
//         $refs[$key] = &$arr[$key];
//     }
//     return $refs;
// }

        // $args = [];
        // $ref = new \ReflectionMethod($this, $method);
        // foreach ($funcArgs as $i => &$arg) {
        //     $args[$i] =& $arg;
        // // foreach ($ref->getParameters() as $i => $arg) {
        //     // if ($arg->isPassedByReference()) {
        //     //     $args[$i] =& $funcArgs[$i];
        //     // } else {
        //     //     $args[$i] = $funcArgs[$i];
        //     // }
        // }
        // $args = valRef($funcArgs);
        foreach($funcArgs as &$arg) {}
        // prd($funcArgs,1);

        // finally call private method that comes from ArrayTrait
        // return $this->{$method}(...$funcArgs);
        return call_user_func_array([$this, 'removeAt'], [&$funcArgs]);
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

    protected static final function throwArgumentCountException(string $method, int $argsNum, int $argsNumMin, bool $showtip = true): void
    {
        $message = sprintf('%s() requires at least %s argument%s, %s given',
            ($methodPath = static::class .'::'. $method), $argsNumMin, ($argsNumMin > 1 ? 's' : ''), $argsNum);
        if ($showtip) {
            $tip = self::getMethodTip($methodPath, $method);
            if ($tip != null) {
                $message .= " [tip => {$method}{$tip}]";
            }
        }
        throw new ArgumentCountException($message);
    }

    protected static final function throwArgumentException(string $method, int $argNum, string $argType, $input, bool $showtip = true): void
    {
        if (strpos($argType, '|')) {
            [$argType, $type] = explode('|', $argType);
        } else {
            $type = Type::get($input, $argType);
        }

        $message = sprintf('Argument %s given to %s() must be %s, %s given', $argNum,
            ($methodPath = static::class .'::'. $method), $argType, $type);
        if ($showtip) {
            $tip = self::getMethodTip($methodPath, $method);
            if ($tip != null) {
                $message .= " [tip => {$method}{$tip}]";
            }
        }
        throw new ArgumentException($message);
    }

    private static final function getMethodTip(string $methodPath, string &$method = null): ?string
    {
        static $tips;
        if ($tips == null) {
            $tips = require_once __dir__ .'/data/tips.php';
        }
        $method = substr($methodPath, strpos($methodPath, '\\') + 1);
        return $tips[$method] ?? null;
    }



    // protected final function toCallArgument($argument): string
    // {
    //     if (is_null($argument)) return 'null';
    //     if (is_bool($argument)) return $argument ? 'true' : 'false';
    //     if (is_string($argument)) return "'{$argument}'";
    //     if (is_array($argument)) return 'Array[...]';
    //     if (is_object($argument)) return 'Object(...)';
    //     return (string) $argument;
    // }
}
