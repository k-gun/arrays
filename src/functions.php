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

namespace xo;

use xo\{Map, Set, Tuple, Collection, StringObject, NumberObject};

/**
 * Map.
 * @param  any ...$arguments
 * @return xo\Map
 */
function map(...$arguments): Map
{
    return new Map(...$arguments);
}

/**
 * Set.
 * @param  any ...$arguments
 * @return xo\Set
 */
function set(...$arguments): Set
{
    return new Set(...$arguments);
}

/**
 * Tuple.
 * @param  any ...$arguments
 * @return xo\Tuple
 */
function tuple(...$arguments): Tuple
{
    return new Tuple(...$arguments);
}

/**
 * Collection.
 * @param  any ...$arguments
 * @return xo\Collection
 */
function collection(...$arguments): Collection
{
    return new Collection(...$arguments);
}

/**
 * String.
 * @param  string $value
 * @return xo\StringObject
 */
function string($value): StringObject
{
    return new StringObject($value);
}

/**
 * Number.
 * @param  numeric $value
 * @return xo\NumberObject
 */
function number($value): NumberObject
{
    return new NumberObject($value);
}
