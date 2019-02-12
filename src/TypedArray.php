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

use xo\{AbstractArray, Type};
use xo\exception\TypeException;

/**
 * @package xo
 * @object  xo\Map
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class TypedArray extends AbstractArray
{
    /**
     * Type.
     * @var string
     */
    protected $type;

    /**
     * Read only.
     * @var bool
     */
    protected $readOnly;

    /**
     * Allow nulls.
     * @var bool
     */
    protected $allowNulls;

    /**
     * Constructor.
     * @param string      $type
     * @param array|null  $items
     * @param string|null $itemsType
     * @param bool        $readOnly
     * @param bool        $allowNulls
     */
    public function __construct(string $type, array $items = null, string $itemsType = null,
        bool $readOnly = false, bool $allowNulls = false)
    {
        $this->type = $type;
        $this->readOnly = $readOnly;
        $this->allowNulls = $allowNulls;

        if ($type != Type::ANY && $items != null) {
            if (!Type::validateItems($this, $items, $itemsType, $error)) {
                throw new TypeException($error);
            }
        }

        parent::__construct($type, $items, $itemsType);
    }

    /**
     * Type.
     * @return string
     */
    public final function type(): string
    {
        return $this->type;
    }

    /**
     * Read only.
     * @return bool
     */
    public final function readOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * Allow nulls.
     * @return bool
     */
    public final function allowNulls(): bool
    {
        return $this->allowNulls;
    }

}
