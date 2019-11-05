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

/**
 * @package xo
 * @object  xo\AbstractObject
 * @author  Kerem Güneş <k-gun@mail.com>
 */
abstract class AbstractObject
{
    /**
     * Get class.
     * @return object
     */
    public final function getClass(): object
    {
        return $this;
    }

    /**
     * Get name.
     * @return string
     */
    public final function getName(): string
    {
        return static::class;
    }

    /**
     * Get short name.
     * @return string
     */
    public final function getShortName(): string
    {
        $name = static::class;

        return substr($name, (false !== $pos = strpos($name, '\\')) ? $pos + 1 : 0);
    }

    /**
     * Get name space.
     * @return string
     * @since  1.5
     */
    public final function getNameSpace(): string
    {
        $name = static::class;

        return substr($name, 0, (false !== $pos = strpos($name, '\\')) ? $pos : 0);
    }

    /**
     * Clone.
     * @return object
     */
    public final function clone(): object
    {
        return clone $this;
    }

    /**
     * Equals.
     * @param  object $object
     * @return bool
     */
    public final function equals(object $object): bool
    {
        return $this === $object;
    }

    /**
     * To value.
     * @return ?any
     */
    public function toValue()
    {
        if (property_exists($this, 'value')) {
            return $this->value;
        }
        return null;
    }

    /**
     * To string.
     * @return string
     */
    public function toString(): string
    {
        return sprintf('object(%s)#%s', $this->getName(), spl_object_id($this));
    }
}
