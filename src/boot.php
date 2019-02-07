<?php
declare(strict_types=1);

namespace arrays;

// function get(array $array, $key, $valueDefault = null) {
//     return $array[$key] ?? $valueDefault;
// }

function to_type($input, $type, bool $nullable = true)
{
    if ($nullable && $input === null) {
        return null;
    }
    settype($input, $type);
    return $input;
}
// function to_string($input, bool $nullable = true): ?string
// {
//     return ($nullable && $input === null) ? null : (string) $input;
// }

function to_array($input): array {
    return (array) ($input ?: []);
}
function to_object($input): object {
    return (object) to_array($input);
}

function is_digit($input, bool $complex = true): bool
{
    if (is_int($input)) {
        return true;
    } elseif (is_string($input) && ctype_digit($input)) {
        return true;
    } elseif ($complex && is_numeric($input)) {
        $input = (string) $input;
        if (strpos($input, '.') === false && ($input < 0)) {
            return true;
        }
    }
    return false;
}

use arrays\{Map, IntMap, FloatMap, StringMap, BoolMap};
use arrays\{Set, IntSet, FloatSet, StringSet, BoolSet};

function map(array $items): Map
{

}

/**
 * Register autoload.
 * @private
 */
(function() {
    $autoload = require_once __dir__ .'/Autoload.php';
    $autoload->register();
})();
