<?php
declare(strict_types=1);

namespace arrays;

use arrays\{Type, TypedArray};

/**
 * @package arrays
 * @object  arrays\Tuple
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Tuple extends TypedArray
{
    public function __construct(array $items = null, string $itemsType = null, bool $allowNulls = false)
    {
        self::$notAllowedMethods = ['search', 'searchLast', 'set', 'add', 'remove', 'removeAt', 'removeAll',
            'append', 'prepend', 'pop', 'unpop', 'shift', 'unshift', 'put', 'push', 'pull', 'find', 'findKey',
            'findIndex', 'replace', 'replaceAt', 'flip', 'pad', 'fill'];

        parent::__construct(Type::TUPLE, $items, $itemsType, $readOnly = true, $allowNulls);
    }

    public function indexOf($value): ?int { return $this->_indexOf($value); }
    public function lastIndexOf($value): ?int { return $this->_lastIndexOf($value); }

    public function has($value): bool { return $this->_has($value); }
    public function hasKey(int $key): bool { return $this->_hasKey($key); }

    public function get($key, $valueDefault = null, bool &$ok = null) { return $this->_get($key, $valueDefault, $ok); }
}
