XO is a library that provides some e(x)tended (o)bjects to build strictly typed arrays such as Map, Set, Tuple or any type of TypedArray's, and also String and Number objects that are not objects natively in PHP.

You can use `Map`, `Set` and `Tuple` to build strict arrays and also use `AnyArray` (`ArrayObject` and `Collection` are just aliases of it) derived from `AbstractArray` that contains many native-equal array methods or `StringObject` and `NumberObject` to use `AbstractScalarObject` interface.

All XO objects extends AbstractObject, so that makes possible to use some basic object methods like `getName()` or `getShortName()`.

### Installation

```bash
composer require k-gun/xo
```

Use can also download and use without Composer including `boot.php`.

```php
include 'path_to_xo/boot.php';
```

### Using Array Objects

Basically there are 5 types of array in XO;

**TypedArray**: `Int` or `String` keyed object derived from **AbstractArray**.

```php
$array = new xo\TypedArray('IntArray', [1, 2, 3], 'int');
$array->sum(); //=> 6

$array->append(4);
$array->sum(); //=> 10

// this will throw a xo\exception\MethodException
// 'cos append() does not take string value
$array->append('4');
```

**AnyArray**: `String` keyed object derived from **TypedArray**.

```php
$map = new xo\AnyArray(['a' => 1, 'b' => 2]);
$map->sum(); //=> 3

// add new item
$map->append(3);
$map->sum(); //=> 6
```

**Map**: `String` keyed object derived from **TypedArray**.

```php
$map = new xo\Map(['a' => 1, 'b' => 2]);
$map->sum(); //=> 3

// this will throw a xo\exception\MethodException
// 'cos append() does not take a key but only value
$map->append(3);

// add new item with key,value pairs using set(), or push() as well
$map->set('c', 3);
$map->sum(); //=> 6
```

**Set**: `Int` keyed object derived from **TypedArray**.

```php
$map = new xo\Set(1, 2);
$map->sum(); //=> 3

// add new item without key
$map->append(3);

// or with key,value pairs using set(), or push() as well
$map->set(2, 3);
$map->sum(); //=> 6
```

**Tuple**: `Int` keyed and `read-only` object derived from **TypedArray**.

```php
$map = new xo\Tuple(1, 2);
$map->sum(); //=> 3

// this will throw a xo\exception\MethodException
// 'cos append() is not allowed for Tuple objects
$map->append(3);
```

But, off course, you can create new typed array objects via `TypedArray` directly like first example or defining new arrays that extend `TypedArray` or other objects such as `Map`, `Set`, `Tuple` or `AnyArray`.

Typed array example;

```php
class IntArray extends xo\TypedArray {
    public function __construct(array $items = null) {
        parent::__construct('IntArray', $items, 'int');
    }
}

$array = new IntArray();
$array->append(1);
$array->append(2);
$array->append('3'); //=> error
```

Yet another example;

```php
class Poll extends xo\TypedArray {
    public function __construct(array $items = null) {
        parent::__construct('Poll', $items, 'array');
    }

    public function getResults(): array {
        return $this->copy()->map(function ($option) {
            return round(array_sum($option) / count($option), 2);
            // or
            // return xo\set($option)->sumAvg(2);
            // return (new xo\Set($option))->sumAvg(2);
        })->sort('asort', function ($a, $b) {
            return $a < $b;
        })->toArray();
    }
}

$poll = new Poll();
$poll->put('option_1', [2, 2, 1]);
$poll->put('option_2', [5, 1, 5]);
$poll->put('option_3', [3, 5, 2]);

var_export($poll->getResults()); //=> ['option_2' => 3.67, 'option_3' => 3.33, 'option_1' => 1.67]
```

If you want to make a untyped array, you can simply use `AnyArray`. So that will provide all methods as well likely on the other arrays.

Here is another with `AnyArray` example like above;

```php
class Poll extends xo\AnyArray {
    public function getResults(): array {
        return $this->copy()->map(function ($option) {
            return round(array_sum($option) / count($option), 2);
        })->sort('asort', function ($a, $b) {
            return $a < $b;
        })->toArray();
    }
}
```

### Custom Arrays

Creating custom arrays;

```php
class User {
    private $id;
    public function __construct(int $id = null) {
        $this->id = $id;
    }
}

class Users extends xo\TypedArray {
    public function __construct(array $items = null) {
        parent::__construct('Users', $items, User::class);
    }
}

$users = new Users();
$users->add(new User(1));
$users->add(new User(2));
$users->add(new User(null));

var_dump($users->copy()->filter(function (User $user) {
    return !is_null($user->id);
})->count()); //=> int(2)

// this will throw a xo\exception\ValueException
// 'cos Users accepts User type values only
$users->add('boom!');
```

Besides, it also possible with `Set` or `Map`;

```php
class Users extends xo\Set {
    public function __construct(array $items = null) {
        parent::__construct($items, User::class);
    }
}

$users = new Users();
$users->add(new User(1));
$users->add(new User(2));
$users->add(new User(null));
...

// or

class Users extends xo\Map {
    public function __construct(array $items = null) {
        parent::__construct($items, User::class);
    }
}

$users = new Users();
$users->put(1, new User(1));
$users->put(2, new User(2));
$users->put('null', new User(null));
...
```

### Using String and Number Objects

```php
$string = new xo\StringObject('Hello, world!');
var_dump($string->test('~hell~')); //=> bool(false)
var_dump($string->test('~hell~i')); //=> bool(true)
var_dump($string->startsWith('He')); //=> bool(true)

$number = new xo\NumberObject(1.555);
var_dump($number->toInt()); //=> int(1)
var_dump($number->toFloat()); //=> float(1.555)
var_dump($number->toFloat(2)); //=> float(1.56)
```

### Objects, Object Methods and Properties

- #### `AbstractObject`

    ```
    abstract class xo\AbstractObject {}

    public final getClass(): object
    public final getName(): string
    public final getShortName(): string
    public final clone(): object
    public final equals(object $object): bool
        throws xo\exception\MethodException
    public toValue(): ?any
    public toString(): string
    ```

- #### `AbstractArray`

    ```
    abstract class xo\AbstractArray extends xo\AbstractObject
        implements xo\ArrayInterface, Countable, IteratorAggregate {}

    use xo\ArrayTrait

    private bool $readOnly
    private bool $allowNulls
    private xo\ArrayObject $items
    private string $itemsType
    private static array $methods
    private static array $invisibleMethods
    protected static array $notAllowedMethods

    public __construct(array $items = null, string $itemsType = null,
        bool $readOnly = false, bool $allowNulls = false)

    public final __call(string $method, array $methodArgs): any
        throws xo\exception\MethodException

    public final prototype(string $method, callable $methodFunc): void
        throws xo\exception\MethodException
    public final readOnly(): bool
    public final allowNulls(): bool
    public final item($key): ?any
    public final items(bool $pair = false): array
    public final itemsType(): string
    public final reset(array $items): self
        throws xo\exception\MutationException
    public final resetItems(array $items): self
        throws xo\exception\MutationException
    public final copy(): xo\ArrayInterface
    public final copyArray(): array
    public final size(): int
    public final empty(): void
    public final isEmpty(): bool
    public final count(): int
    public final countValues(): array
    public final keys(): array
    public final values(): array
    public final first(): ?any
    public final firstKey(): int|string|null
    public final last(): ?any
    public final lastKey(): int|string|null
    public final toArray(bool $normalize = false): array
    public final toObject(): object
    public final toJson(): string
    public final map(callable $func): self
        throws xo\exception\MutationException
    public final reduce($initialValue = null, callable $func = null): any
    public final filter(callable $func = null): self
        throws xo\exception\MutationException
    public final diff(iterable $items, bool $uniq = false): array
    public final uniq(): array
    public final ununiq(): array
    public final uniqs(): array
    public final merge(self $array): self
        throws xo\exception\MutationException,ArgumentException
    public final chunk(int $size, bool $preserveKeys = false): array
    public final slice(int $offset, int $size = null, bool $preserveKeys = false): array
    public final reverse(): self
    public final rand(int $size = 1, bool $useKeys = false)
    public final shuffle(bool $preserveKeys = null): self
        throws xo\exception\MutationException
    public final sort(callable $func = null, callable $ufunc = null, int $flags = 0): self
        throws xo\exception\MutationException
    public final sortNatural(bool $caseSensitive = true, int $flags = 0): self
        throws xo\exception\MutationException
    public final sortLocale(string $locale, callable $func = null, callable $ufunc = null, int $flags = 0): self
        throws xo\exception\MutationException
    public final test(Closure $func): bool
    public final testAll(Closure $func): bool
    public final find(Closure $func): ?any
    public final findKey(Closure $func): int|string|null
    public final findIndex(Closure $func): ?int
    public final keyCheck(int|string $key): void
        throws xo\exception\KeyException
    public final valueCheck(any $value): void
        throws xo\exception\ValueException
    public final keyValueCheck(int|string $key, any $value): void
        throws xo\exception\KeyException,ValueException
    public final nullCheck(any $value): void
        throws xo\exception\NullException
    public final methodCheck(string $method): void
        throws xo\exception\MethodException
    public final readOnlyCheck(): void
        throws xo\exception\MutationException
    public final generate(bool $reverse = false): Generator
    public final getIterator(): Generator
    public final min(bool $numericsOnly = false): ?any
    public final max(bool $numericsOnly = false): ?any
    public final calc(string $operator, int $round = null, int &$valueCount = null): ?number
        throws xo\ArrayException
    public final calcAvg(string $operator, int $round = null, int &$valueCount = null): ?float
        throws xo\ArrayException
    public final sum(int $round = null, int &$valueCount = null): ?number
    public final sumAvg(int $round = null, int &$valueCount = null): ?float

    private final executeCommand(string $command, &...$arguments): void
        throws xo\exception\MutationException,NullException
        throws xo\ArrayException
    ```

- #### `TypedArray`

    ```
    class xo\TypedArray extends xo\AbstractArray {}

    protected string $type;

    public __construct(string $type, array $items = null, string $itemsType = null,
        bool $readOnly = false, bool $allowNulls = false)
        throws xo\exception\TypeException

    public final type(): string
    ```

- #### `AnyArray`

    ```
    class xo\AnyArray extends xo\TypedArray {}

    protected static array $notAllowedMethods = []

    public __construct(array $items = null, bool $readOnly = false)

    public search(any $value): int|string|null
    public searchLast(any $value): int|string|null
    public indexOf(any $value): ?int
    public lastIndexOf(any $value): ?int
    public has(any $value): bool
    public hasKey(int|string $key): bool
    public set(int|string $key, any $value, int &$size = null): self
    public get(int|string $key, any $valueDefault = null, bool &$found = null): ?any
    public add(any $value): self
    public remove(any $value, bool &$found = null): self
    public removeAt(int|string $key, bool &$found = null): self
    public removeAll(array $values, int &$count = null): self
    public append(any $value, int &$size = null): self
    public prepend(any $value, int &$size = null): self
    public pop(int &$size = null): ?any
    public unpop(any $value, int &$size = null): self
    public shift(int &$size = null): ?any
    public unshift(any $value, int &$size = null): self
    public put(int|string $key, any $value): self
    public push(int|string $key, any $value): self
    public pull(int|string $key, any $valueDefault = null, bool &$found = null): ?any
    public replace(any $value, any $replaceValue, bool &$found = null): self
    public replaceAt(int|string $key, any $replaceValue, bool &$found = null): self
    public flip(): self
        throws xo\ArrayException
    public pad(int $times, any $value, int $offset = null): self
    ```

- #### `Map`

    ```
    class xo\Map extends xo\TypedArray {}

    protected static array $notAllowedMethods = ['flip', 'add', 'append', 'prepend', 'unpop',
        'unshift', 'flip', 'pad']

    public __construct(array $items = null, string $itemsType = null,
        bool $readOnly = false, bool $allowNulls = false)

    public search(any $value): int|string|null
    public searchLast(any $value): int|string|null
    public indexOf(any $value): ?int
    public lastIndexOf(any $value): ?int
    public has(any $value): bool
    public hasKey(string $key): bool
    public set(string $key, any $value, int &$size = null): self
    public get(string $key, any $valueDefault = null, bool &$found = null): ?any
    public remove(any $value, bool &$found = null): self
    public removeAt(string $key, bool &$found = null): self
    public removeAll(array $values, int &$count = null): self
    public pop(int &$size = null): ?any
    public shift(int &$size = null): ?any
    public put(string $key, any $value): self
    public push(string $key, any $value): self
    public pull(string $key, any $valueDefault = null, bool &$found = null): ?any
    public replace(any $value, any $replaceValue, bool &$found = null): self
    public replaceAt(string $key, any $replaceValue, bool &$found = null): self
    ```

- #### `Set`

    ```
    class xo\Set extends xo\TypedArray {}

    protected static array $notAllowedMethods = ['flip']

    public __construct(array $items = null, string $itemsType = null,
        bool $readOnly = false, bool $allowNulls = false)

    public search(any $value): int|string|null
    public searchLast(any $value): int|string|null
    public indexOf(any $value): ?int
    public lastIndexOf(any $value): ?int
    public has(any $value): bool
    public hasKey(int $key): bool
    public set(int $key, any $value, int &$size = null): self
    public get(int $key, any $valueDefault = null, bool &$found = null): ?any
    public add(any $value): self
    public remove(any $value, bool &$found = null): self
    public removeAt(int $key, bool &$found = null): self
    public removeAll(array $values, int &$count = null): self
    public append(any $value, int &$size = null): self
    public prepend(any $value, int &$size = null): self
    public pop(int &$size = null): ?any
    public unpop(any $value, int &$size = null): self
    public shift(int &$size = null): ?any
    public unshift(any $value, int &$size = null): self
    public put(int $key, any $value): self
    public push(int $key, any $value): self
    public pull(int $key, any $valueDefault = null, bool &$found = null): ?any
    public replace(any $value, any $replaceValue, bool &$found = null): self
    public replaceAt(int $key, any $replaceValue, bool &$found = null): self
    public pad(int $times, any $value, int $offset = null): self
    ```

- #### `Tuple`

    ```
    class xo\Tuple extends xo\TypedArray {}

    protected static array $notAllowedMethods = ['reset', 'resetItems', 'empty', 'map', 'filter', 'merge',
        'reverse', 'shuffle', 'search', 'searchLast', 'set', 'add', 'remove', 'removeAt', 'removeAll',
        'append', 'prepend', 'pop', 'unpop', 'shift', 'unshift', 'put', 'push', 'pull', 'replace', 'replaceAt',
        'flip', 'pad']

    public __construct(array $items = null, string $itemsType = null, bool $allowNulls = false)

    public indexOf(any $value): ?int
    public lastIndexOf(any $value): ?int
    public has(any $value): bool
    public hasKey(int $key): bool
    public get(int $key, any $valueDefault = null, bool &$found = null): ?any
    ```

- #### `ArrayObject` and `Collection`

    ```
    // these are just aliased classes
    class xo\ArrayObject extends xo\AnyArray {}
    class xo\Collection extends xo\AnyArray {}
    ```

- #### `AbstractScalarObject`

    ```
    abstract class xo\AbstractScalarObject extends xo\AbstractObject {}

    protected scalar $value;
    protected string $valueType;

    public __construct(scalar $value)
        throws xo\exception\ArgumentTypeException

    public final value(): scalar
    public final valueType(): string
    public final equalTo(scalar $value): bool
    public final size(bool $multiByte = false): int
    public toString(): string
    ```

- #### `StringObject`

    ```
    class xo\StringObject extends xo\AbstractScalarObject {}

    public string const TRIM_CHARS = " \t\n\r\0\x0B"

    public __construct(string $value)
        throws xo\exception\ArgumentTypeException

    public final test(string $pattern): ?bool
    public final match(string $pattern, int $flags = 0): ?array
    public final matchAll(string $pattern, int $flags = 0): ?array
    public final indexOf(string $search, bool $caseSensitive = true): ?int
    public final lastIndexOf(string $search, bool $caseSensitive = true): ?int

    public final trim(string $chars = null, int $side = 0): string
    public final trimLeft(string $chars = null): string
    public final trimRight(string $chars = null): string
    public final trimSearch(string $search, bool $caseSensitive = true, int $side = 0): string
    public final trimSearches(array[string] $searches, bool $caseSensitive = true, int $side = 0): string

    public final compare(string $value): int
    public final compareLocale(string $locale, string $value): int
    public final contains(string $search, bool $caseSensitive = true): bool
    public final containsAny(array[string] $searches, bool $caseSensitive = true): bool
    public final containsAll(array[string] $searches, bool $caseSensitive = true): bool
    public final startsWith(string $search): bool
    public final endsWith(string $search): bool
    ```

- #### `NumberObject`

    ```
    class xo\NumberObject extends xo\AbstractScalarObject {}

    public __construct(numeric $value)
        throws xo\exception\ArgumentTypeException

    public toInt(): int
    public toFloat(int $round = null): float
    ```

### Static and Util Objects

- #### `StaticClass`

    ```
    static class xo\StaticClass {}

    public final __construct()
        throws xo\StaticClassException
    ```

- #### `Type`

    ```
    static final class xo\Type extends xo\StaticClass {}

    public const ANY = 'Any'
    public const MAP = 'Map'
    public const SET = 'Set'
    public const TUPLE = 'Tuple'

    public static validateItems(object $object, string $type, array $items, string $itemsType = null,
        bool $allowNulls, string &$error = null): bool
    public static get(any $input, bool $objectCheck = false): string
    public static export(any $input): string
    public static makeArray(any $input): array
    public static makeObject(any $input): object
    public static isBasic(string $type): bool
    public static isDigit(any $input, bool $complex = true): bool
    public static isTuple(any $input): bool
    public static isMapLike(any $input): bool
    public static isSetLike(any $input): bool
    ```


- #### `Util`

    ```
    static class xo\util\Util extends xo\StaticClass {}
    ```

- #### `ArrayUtil`

    ```
    static class xo\util\ArrayUtil extends xo\util\Util {}

    public static final keyCheck(int|string $key): ?string
        throws xo\util\UtilException
    public static final isSequentialArray(array $array): bool
    public static final isAssociativeArray(array $array): bool
    public static final set(array &$array, int|string $key, any $value): array
        throws xo\util\UtilException
    public static final get(array $array, int|string $key, any $valueDefault = null): ?any
        throws xo\util\UtilException
    public static final getAll(array $array, array[int|string] $keys, any $valueDefault = null): array
        throws xo\util\UtilException
    public static final pull(array &$array, int|string $key, any $valueDefault = null): ?any
        throws xo\util\UtilException
    public static final pullAll(array &$array, array[int|string] $keys, any $valueDefault = null): array
        throws xo\util\UtilException
    public static final test(array $array, Closure $func): bool
    public static final testAll(array $array, Closure $func): bool
    public static final rand(array $items, int $size = 1, bool $useKeys = false)
        throws xo\util\UtilException
    public static final shuffle(array &$items, bool $preserveKeys = false): array
    public static final sort(array &$array, callable $func = null, callable $ufunc = null, int $flags = 0): array
        throws xo\util\UtilException
    public static final include(array $array, array $keys): array
    public static final exclude(array $array, array $keys): array
    public static final first(array $array, any $valueDefault = null): ?any
    public static final last(array $array, any $valueDefault = null): ?any
    public static final getInt(array $array, int|string $key, int $valueDefault = null): int
    public static final getFloat(array $array, int|string $key, float $valueDefault = null): float
    public static final getString(array $array, int|string $key, string $valueDefault = null): string
    public static final getBool(array $array, int|string $key, bool $valueDefault = null): bool
    ```

### Shortcut Functions

    map(...$arguments): xo\Map
    set(...$arguments): xo\Set
    tuple(...$arguments): xo\Tuple
    collection(...$arguments): xo\Collection
    string(string $value): xo\StringObject
    number(numeric $value): xo\NumberObject
