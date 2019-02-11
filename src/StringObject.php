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

namespace xobjects;

use xobjects\AbstractScalarObject;

/**
 * @package xobjects
 * @object  xobjects\StringObject
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class StringObject extends AbstractScalarObject
{
    // public const TRIM_CHARS = " \t\n\r\0\x0B";

    public function __construct(string $value = null)
    {
        $this->setValue($value);
    }

    public final function test(string $pattern, bool $esc = false, string $escChars = null): bool {}
    public final function match(string $pattern, int $flags = null, bool $esc = false, string $escChars = null): bool {}
    public final function matchAll(string $pattern, int $flags = null, bool $esc = false, string $escChars = null): bool {}

    public final function indexOf(string $search, bool $caseSensitive = true): ?int {}
    public final function lastIndexOf(string $search, bool $caseSensitive = true): ?int {}

    public final function trim(string $chars = null, int $side = 0): string {}
    public final function trimLeft(string $chars = null): string {}
    public final function trimRight(string $chars = null): string {}

    public final function trimWord(string $word, int $side = 0): string {}
    public final function trimWords(array $words, int $side = 0): string {}

    public final function replace($search, $replace): string {}
}
