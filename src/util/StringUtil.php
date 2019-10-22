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

namespace xo\util;

use xo\util\Util;

/**
 * @package xo\util
 * @object  xo\util\StringUtil
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class StringUtil extends Util
{
    /**
     * Compare.
     * @param  string $source1
     * @param  string $source2
     * @return int
     */
    public static final function compare(string $source1, string $source2): int
    {
       return ($source1 > $source2) - ($source1 < $source2);
    }

    /**
     * Compare locale.
     * @param  string $locale
     * @param  string $source1
     * @param  string $source2
     * @return int
     */
    public static final function compareLocale(string $locale, string $source1, string $source2): int
    {
        $localeDefault = setlocale(LC_COLLATE, 0);
        setlocale(LC_COLLATE, $locale);
        $result = strcoll($source1, $source2);
        setlocale(LC_COLLATE, $localeDefault); // reset locale
        return $result;
    }

    /**
     * Contains.
     * @param  string $source
     * @param  string $search
     * @param  bool   $caseSensitive
     * @return bool
     */
    public static final function contains(string $source, string $search, bool $caseSensitive = true): bool
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
    public static final function containsAny(string $source, array $searches, bool $caseSensitive = true): bool
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
    public static final function containsAll(string $source, array $searches, bool $caseSensitive = true): bool
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
    public static final function startsWith(string $source, string $search): bool
    {
        return ($search === substr($source, 0, strlen($search)));
    }

    /**
     * Ends with.
     * @param  string $source
     * @param  string $search
     * @return bool
     */
    public static final function endsWith(string $source, string $search): bool
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
    public static final function trimSearch(string $source, string $search, bool $caseSensitive = true,
        int $side = 0): string
    {
        $search = preg_quote($search);
        $pattern = sprintf('~%s~%s', $side == 0 ? "^{$search}|{$search}$" : (
            $side == 1 ? "^{$search}" : "{$search}$"), $caseSensitive ? '' : 'i');

        return (string) preg_replace($pattern, '', $source);
    }

    /**
     * Is utf.
     * @param  ?string $source
     * @param  int     $bits
     * @return bool
     * @since  1.4
     */
    public static final function isUtf(?string $source, int $bits = 8): bool
    {
        // 0x00 - 0x10FFFF @link https://en.wikipedia.org/wiki/Code_point
        return !!($source && mb_check_encoding($source, 'UTF-'. $bits));
    }

    /**
     * Is ascii.
     * @param  ?string $source
     * @return bool
     * @since  1.4
     */
    public static final function isAscii(?string $source): bool
    {
        // 0x00 - 0x7F (or extended 0xFF) @link https://en.wikipedia.org/wiki/Code_point
        return !!($source && mb_check_encoding($source, 'ASCII'));
    }

    /**
     * Is binary.
     * @param  ?string $source
     * @return bool
     * @since  1.4
     */
    public static final function isBinary(?string $source): bool
    {
        return !!($source && !ctype_print($source));
    }

    /**
     * Is base64.
     * @param  ?string $source
     * @return bool
     * @since  1.4
     */
    public static final function isBase64(?string $source): bool
    {
        return !!($source && base64_encode(''. base64_decode($source, true)) == $source);
    }
}
