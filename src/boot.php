<?php
declare(strict_types=1);

namespace arrays;

// function get(array $array, $key, $valueDefault = null) {
//     return $array[$key] ?? $valueDefault;
// }

function string($input, bool $nullable = true): ?string
{
    return ($nullable && $input === null) ? null : (string) $input;
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
