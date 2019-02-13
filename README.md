XO is a library that provides some e(x)tended (o)bjects to build strictly typed arrays such as Map, Set, Tuple or any type of TypedArray's, and also String and Number objects that are not objects natively in PHP.

You can use `Map`, `Set` and `Tuple` to build strict arrays and also use `AnyArray` (`ArrayObject` and `Collection` are just aliases of it) derived from `AbstractArray` that contains many native-equal array methods or `StringObject` and `NumberObject` to use `AbstractScalarObject` interface.

All XO objects extends AbstractObject, so that makes possible to use some basic object methods like `getName()` or `getShortName()`.

#### Installation

```bash
composer require k-gun/xo
```

Use can also download and use without Composer including `boot.php`.

```php
include 'path_to_xo/boot.php';
```

#### Using Array Objects

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

#### Custom Arrays

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

#### Using String and Number Objects

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
