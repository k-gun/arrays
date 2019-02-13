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

use RuntimeException;

/**
 * @package xo
 * @object  xo\Autoload
 * @author  Kerem Güneş <k-gun@mail.com>
 */
final class Autoload
{
    /**
     * Instance.
     * @var xo\Autoload
     */
    private static $instance;

    /**
     * Is registered.
     * @var bool
     */
    private static $isRegistered = false;

    /**
     * Forbidding idle init & copy actions.
     */
    private function __construct() {}
    private function __clone() {}

    /**
     * Init.
     * @return self
     */
    public static function init(): self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Register.
     * @return bool
     * @throws RuntimeException
     */
    public function register(): bool
    {
        if (self::$isRegistered) {
            return true;
        }

        self::$isRegistered = spl_autoload_register(function($object) {
            $namespace = __namespace__;
            $directory = __dir__;

            // xo objects only
            if (strpos($object, $namespace) !== 0) {
                return;
            }

            $object = substr($object, strlen($namespace) + 1);
            $objectFile = strtr("{$directory}/{$object}.php", ['\\' => '/']);

            if (!file_exists($objectFile)) {
                throw new RuntimeException("Object file '{$objectFile}' not found");
            }

            require $objectFile;
        });

        return self::$isRegistered;
    }
}

// shorcut for require
return Autoload::init();
