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
use xobjects\exception\ArgumentException;
use xobjects\util\StringUtil;

/**
 * @package xobjects
 * @object  xobjects\StringObject
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class StringObject extends AbstractScalarObject
{
    /**
     * Trim chars.
     * @const string
     */
    public const TRIM_CHARS = " \t\n\r\0\x0B";

    /**
     * Constructor.
     * @param string|null $value
     */
    public function __construct(string $value = null)
    {
        parent::__construct($value);
    }

    /**
     * Test.
     * @param  string $pattern
     * @return ?bool
     */
    public final function test(string $pattern): ?bool
    {
        return (false !== $result =@ preg_match($pattern, $this->value)) ?
            !!$result : null; // error
    }

    /**
     * Match.
     * @param  string $pattern
     * @param  int    $flags
     * @return ?array
     */
    public final function match(string $pattern, int $flags = 0): ?array
    {
        return (false !== $result =@ preg_match($pattern, $this->value, $matches, $flags))
            ? $matches : null; // error
    }

    /**
     * Match all.
     * @param  string $pattern
     * @param  int    $flags
     * @return ?array
     */
    public final function matchAll(string $pattern, int $flags = 0): ?array
    {
        return (false !== $result =@ preg_match_all($pattern, $this->value, $matches, $flags))
            ? $matches : null; // error
    }

    /**
     * Index of.
     * @param  string $search
     * @param  bool   $caseSensitive
     * @return ?int
     */
    public final function indexOf(string $search, bool $caseSensitive = true): ?int
    {
        if ($search === '') {
            throw new ArgumentException('Empty search value given');
        }

        return (false !== $index = ($caseSensitive ? strpos((string) $this->value, $search)
            : stripos((string) $this->value, $search))) ? $index : null;
    }

    /**
     * Last index of.
     * @param  string $search
     * @param  bool   $caseSensitive
     * @return ?int
     */
    public final function lastIndexOf(string $search, bool $caseSensitive = true): ?int
    {
        if ($search === '') {
            throw new ArgumentException('Empty search value given');
        }

        return (false !== $index = ($caseSensitive ? strrpos((string) $this->value, $search)
            : strripos((string) $this->value, $search))) ? $index : null;
    }

    /**
     * Trim.
     * @param  string|null $chars
     * @param  int         $side
     * @return string
     */
    public final function trim(string $chars = null, int $side = 0): string
    {
        $value = (string) $this->value;
        $chars = $chars ?? self::TRIM_CHARS;

        return $side == 0 ? trim($value, $chars) : (
            $side == 1 ? ltrim($value, $chars) : rtrim($value, $chars));
    }

    /**
     * Trim left.
     * @param  string|null $chars
     * @return string
     */
    public final function trimLeft(string $chars = null): string
    {
        return $this->trim($chars, 1);
    }

    /**
     * Trim right.
     * @param  string|null $chars
     * @return string
     */
    public final function trimRight(string $chars = null): string
    {
        return $this->trim($chars, 2);
    }

    /**
     * Trim search.
     * @param  string $search
     * @param  bool   $caseSensitive
     * @param  int    $side
     * @return string
     */
    public final function trimSearch(string $search, bool $caseSensitive = true, int $side = 0): string
    {
        return StringUtil::trimSearch((string) $this->value, $search, $caseSensitive, $side);
    }

    /**
     * Trim searches.
     * @param  string $searches
     * @param  bool   $caseSensitive
     * @param  int    $side
     * @return string
     */
    public final function trimSearches(array $searches, bool $caseSensitive = true, int $side = 0): string
    {
        $value = (string) $this->value;
        foreach ($searches as $search) {
            $value = StringUtil::trimSearch($value, $search, $caseSensitive, $side);
        }

        return $value;
    }

    /**
     * Contains.
     * @param  string $search
     * @param  bool   $caseSensitive
     * @return bool
     */
    public final function contains(string $search, bool $caseSensitive = true): bool
    {
        return StringUtil::contains((string) $this->value, $search, $caseSensitive);
    }

    /**
     * Contains any.
     * @param  array  $searches
     * @param  bool   $caseSensitive
     * @return bool
     */
    public final function containsAny(array $searches, bool $caseSensitive = true): bool
    {
        return StringUtil::containsAny((string) $this->value, $searches, $caseSensitive);
    }

    /**
     * Contains all.
     * @param  array  $searches
     * @param  bool   $caseSensitive
     * @return bool
     */
    public final function containsAll(array $searches, bool $caseSensitive = true): bool
    {
        return StringUtil::containsAny((string) $this->value, $searches, $caseSensitive);
    }

    /**
     * Equal to.
     * @param  string $value
     * @return bool
     */
    public final function equalTo(string $value): bool
    {
        return $this->value !== null && $this->value === $value;
    }

    /**
     * Starts with.
     * @param  string $search
     * @return bool
     */
    public final function startsWith(string $search): bool
    {
        return StringUtil::startsWith((string) $this->value, $search);
    }

    /**
     * Ends with.
     * @param  string $search
     * @return bool
     */
    public final function endsWith(string $search): bool
    {
        return StringUtil::endsWith((string) $this->value, $search);
    }
}
