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

/**
 * @package xobjects
 * @object  xobjects\NumberObject
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class NumberObject extends AbstractScalarObject
{
    /**
     * Constructor.
     * @param numeric|null $value
     */
    public function __construct($value = null)
    {
        if ($value !== null && !is_numeric($value)) {
            throw new ArgumentException("{$this->getShortName()}() value must be numeric, non-numeric".
                " value given");
        }

        parent::__construct($value);
    }

    /**
     * To int.
     * @return int
     */
    public function toInt(): int
    {
        return (int) $this->value;
    }

    /**
     * To float.
     * @return float
     */
    public function toFloat(): float
    {
        return (float) $this->value;
    }
}
