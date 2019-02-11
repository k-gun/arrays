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

namespace xobjects\util;

use xobjects\util\Util;

/**
 * @package xobjects\util
 * @object  xobjects\util\StringUtil
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class StringUtil extends Util
{
    /**
     * Contains.
     * @param  string $source
     * @param  string $search
     * @param  bool   $caseSensitive
     * @return bool
     */
    public static function contains(string $source, string $search, bool $caseSensitive = true): bool
    {
        return (false !== ($caseSensitive ? strpos($source, $search) : stripos($source, $search)));
    }

    /**
     * Contains any.
     * @param  string $source
     * @param  array  $search
     * @param  bool   $caseSensitive
     * @return bool
     */
    public static function containsAny(string $source, array $searches, bool $caseSensitive = true): bool
    {
        foreach ($searches as $search) {
            if (self::contains($source, $search, $caseSensitive)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Contains all.
     * @param  string $source
     * @param  array  $search
     * @param  bool   $caseSensitive
     * @return bool
     */
    public static function containsAll(string $source, array $searches, bool $caseSensitive = true): bool
    {
        foreach ($searches as $search) {
            if (!self::contains($source, $search, $caseSensitive)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Starts with.
     * @param  string $source
     * @param  string $search
     * @return bool
     */
    public static function startsWith(string $source, string $search): bool
    {
        return ($search === substr($source, 0, strlen($search)));
    }

    /**
     * Ends with.
     * @param  string $source
     * @param  string $search
     * @return bool
     */
    public static function endsWith(string $source, string $search): bool
    {
        return ($search === substr($source, -strlen($search)));
    }

    /**
     * Trim search.
     * @param  string $source
     * @param  string $search
     * @param  bool   $caseSensitive
     * @param  int    $side
     * @return string
     */
    public static function trimSearch(string $source, string $search, bool $caseSensitive = true,
        int $side = 0): string
    {
        $search = preg_quote($search);
        $pattern = sprintf('~%s~%s', $side == 0 ? "^{$search}|{$search}$" : (
            $side == 1 ? "^{$search}" : "{$search}$"), $caseSensitive ? '' : 'i');

        return (string) preg_replace($pattern, '', $source);
    }
}
