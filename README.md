XO is a library that provides some e(x)tended (o)bjects to build strictly typed arrays such as Map, Set, Tuple or any type of TypedArray's, and also String and Number objects that are not objects natively in PHP.

You can use Map, Set and Tuple to build strict arrays and also use AnyArray (ArrayObject and Collection are just aliases of it) derived from AbstractArray that contains many native-equal array methods or StringObject and NumberObject to use AbstractScalarObject interface.

All XO objects extends AbstractObject, so that makes possible to use some basic object methods like `getName()` or `getShortName()`.

### Installation

```bash
composer require k-gun/xo
```

### Using Array Objects

Basically there are 4 type of array in XO;

**Map: String key-ed objects derived from `AnyArray`.**
