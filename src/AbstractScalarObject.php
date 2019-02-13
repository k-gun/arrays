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

use xo\{AbstractObject, Type};
use xo\exception\ArgumentTypeException;

/**
 * @package xo
 * @object  xo\AbstractScalarObject
 * @author  Kerem Güneş <k-gun@mail.com>
 */
abstract class AbstractScalarObject extends AbstractObject
{
    /**
     * Value.
     * @var scalar
     */
    protected $value;

    /**
     * Value type.
     * @var string
     */
    protected $valueType;

    /**
     * Construct.
     * @param scalar $value
     * @throws xo\exception\ArgumentTypeException
     */
    public function __construct($value)
    {
        if (!is_scalar($value)) {
            throw new ArgumentTypeException('Given value is not scalar');
        }

        $this->value = $value;
        $this->valueType = Type::get($value);
    }

    /**
     * Value.
     * @return scalar
     */
    public final function value()
    {
        return $this->value;
    }

    /**
     * Value type.
     * @return string
     */
    public final function valueType(): string
    {
        return $this->valueType;
    }

    /**
     * Equal to.
     * @param  scalar $value
     * @return bool
     */
    public final function equalTo($value): bool
    {
        return $this->value === $value;
    }

    /**
     * Size
     * @param  bool $multiByte
     * @return int
     */
    public final function size(bool $multiByte = false): int
    {
        $value = (string) $this->value;
        return !$multiByte ? strlen($value) : mb_strlen($value);
    }

    /**
     * To string.
     * @return string
     */
    public function toString(): string
    {
        $value = $this->value;
        if ($this->valueType == 'float') {
            $value = json_encode($value, JSON_PRESERVE_ZERO_FRACTION);
        }
        return (string) $value;
    }
}

