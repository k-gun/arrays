<?php
declare(strict_types=1);

namespace arrays;

function is_sequential_array(array $array): bool
{
    return !$array || array_keys($array) === range(0, count($array) - 1);
}
function is_associative_array(array $array): bool
{
    if (count($array) !== count(array_filter(array_keys($array), 'is_string'))) {
        return false;
    }
    // @link https://stackoverflow.com/a/6968499/362780
    return !$array || ($array !== array_values($array)); // speed-wise
    // $array = array_keys($array); return ($array !== array_keys($array)); // memory-wise:
}

/**
 * Register autoload.
 * @private
 */
(function() {
    $autoload = require_once __dir__ .'/Autoload.php';
    $autoload->register();
})();
