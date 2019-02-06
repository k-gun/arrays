<?php
declare(strict_types=1);

namespace arrays;

use arrays\StaticClassException;

/**
 * @package arrays
 * @object  arrays\StaticClass
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class StaticClass
{
    /**
     * Constructor.
     * @throws arrays\StaticClassException
     */
    final public function __construct()
    {
        throw new StaticClassException('Cannot initialize static class '. Arrays::class);
    }
}
