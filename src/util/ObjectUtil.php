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

use xo\util\{Util, UtilException};
use ReflectionClass, ReflectionException;
use Error;

/**
 * @package xo\util
 * @object  xo\util\ObjectUtil
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class ObjectUtil extends Util
{
    /**
     * Get name.
     * @param  string|object $class
     * @return string
     * @throws xo\util\UtilException
     */
    public static final function getName($class): string
    {
        if (is_string($class)) return $class;
        if (is_object($class)) return get_class($class);

        throw new UtilException('Invalid $class argument, string and object arguments allowed only');
    }

    /**
     * Get short name.
     * @param  string|object $class
     * @return string
     */
    public static final function getShortName($class): string
    {
        $name = self::getName($class);
        $nameIndex = strrpos($name, '\\');

        return substr($name, ($nameIndex !== false) ? $nameIndex + 1 : 0);
    }

    /**
     * Get namespace.
     * @param  string|object $class
     * @param  bool          $base
     * @return string
     */
    public static final function getNamespace($class, bool $base = false): string
    {
        $name = self::getName($class);
        $nameIndex = $base ? strpos($name, '\\') : strrpos($name, '\\');

        return substr($name, 0, ($nameIndex !== false) ? $nameIndex : 0);
    }

    /**
     * Get reflection.
     * @param  string|object $class
     * @return ReflectionClass
     * @throws xo\util\UtilException
     */
    public static final function getReflection($class): ReflectionClass
    {
        try {
            return new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new UtilException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get constant.
     * @param  string|object $class
     * @param  string        $name
     * @return ?array
     */
    public static final function getConstant($class, string $name): ?array
    {
        return self::getConstants($class, true, $name)[$name] ?? null;
    }

    /**
     * Get constant value.
     * @param  string|object $class
     * @param  string        $name
     * @return any
     */
    public static final function getConstantValue($class, string $name)
    {
        return self::getConstants($class, true, $name)[$name]['value'] ?? null;
    }

    /**
     * Get constants registry.
     * @param  string|object $class
     * @param  bool          $all
     * @param  string        $name @internal
     * @return ?array
     */
    public static final function getConstants($class, bool $all = true, string $name = null): ?array
    {
        $ref = self::getReflection($class);

        foreach ($ref->getReflectionConstants() as $constant) {
            if ($name && $name !== $constant->name) {
                continue;
            }

            if (!$all && !$constant->isPublic()) {
                continue;
            }

            // Sorry, but no method such getType()..
            preg_match('~\[ (?<visibility>\w+) (?<type>\w+) .+ \]~', $constant->__toString(),
                $match);

            // Using name as key, since all names will be overridden internally in children.
            $ret[$constant->name] = [
                'value'      => $constant->getValue(),
                'type'       => $match['type'],
                'visibility' => $match['visibility'],
                'class'      => $constant->getDeclaringClass()->name
            ];
        }

        return $ret ?? null;
    }

    /**
     * Get constant names.
     * @param  string|object $class
     * @param  bool          $all
     * @return ?array
     */
    public static final function getConstantNames($class, bool $all = true): ?array
    {
        $ref = self::getReflection($class);

        if ($all) {
            // Seems doesn't matter constant visibility for getConstants().
            $ret = array_keys($ref->getConstants());
        } else {
            foreach ($ref->getReflectionConstants() as $constant) {
                if ($constant->isPublic()) {
                    $ret[] = $constant->name;
                }
            }
        }

        return $ret ?? null;
    }

    /**
     * Get property.
     * @param  string|object $class
     * @param  string        $name
     * @return ?array
     */
    public static final function getProperty($class, string $name): ?array
    {
        return self::getProperties($class, true, $name)[$name] ?? null;
    }

    /**
     * Get property value.
     * @param  string|object $class
     * @param  string        $name
     * @return any
     */
    public static final function getPropertyValue($class, string $name)
    {
        return self::getProperties($class, true, $name)[$name]['value'] ?? null;
    }

    /**
     * Get properties registry.
     * @param  string|object $class
     * @param  bool          $all
     * @param  string        $name @internal
     * @return array
     */
    public static final function getProperties($class, bool $all = true, string $name = null): array
    {
        $ref = self::getReflection($class);

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        foreach ($ref->getProperties($filter) as $property) {
            if ($name && $name !== $property->name) {
                continue;
            }

            $value = $type = $nullable = null;
            if ($property->hasType()) {
                $type = $property->getType()->getName();
                $nullable = $property->getType()->allowsNull();
            }

            $visibility = $property->isPublic() ? 'public' : (
                $property->isPrivate() ? 'private' : 'protected'
            );

            // Try, cos "Typed property $foo must not be accessed before initialization".
            try {
                // For getValue() and others below.
                if (!$visibility != 'public') {
                    $property->setAccessible(true);
                }

                $value = $property->getValue($class);
            } catch (Error $e) {}

            // Using name as key, since all names will be overridden internally in children.
            $ret[$property->name] = [
                'value'       => $value,
                'type'        => $type,
                'nullable'    => $nullable ?? ($value === null),
                'visibility'  => $visibility,
                'static'      => $property->isStatic(),
                'initialized' => $property->isInitialized($class),
                'class'       => $property->getDeclaringClass()->name
            ];
        }

        return $ret ?? null;
    }

    /**
     * Get property names.
     * @param  string|object $class
     * @param  bool          $all
     * @return ?array
     */
    public static final function getPropertyNames($class, bool $all = true): ?array
    {
        $ref = self::getReflection($class);

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        foreach ($ref->getProperties($filter) as $property) {
            $ret[] = $property->name;
        }

        return $ret ?? null;
    }

    /**
     * Get method.
     * @param  string|object $class
     * @param  string        $name
     * @return ?array
     */
    public static final function getMethod($class, string $name): ?array
    {
        return self::getMethods($class, true, $name)[$name] ?? null;
    }

    /**
     * Get methods.
     * @param  string|object $class
     * @param  bool          $all
     * @param  string        $name @internal
     * @return ?array
     */
    public static final function getMethods($class, bool $all = true, string $name = null): ?array
    {
        $ref = self::getReflection($class);

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        foreach ($ref->getMethods($filter) as $method) {
            if ($name && $name !== $method->name) {
                continue;
            }

            $return = null;
            if ($method->hasReturnType()) {
                $return = $method->getReturnType()->getName();
            }

            $visibility = $method->isPublic() ? 'public' : (
                $method->isPrivate() ? 'private' : 'protected'
            );

            // Using name as key, since all names will be overridden internally in children.
            $ret[$method->name] = [
                'visibility' => $visibility,
                'static'     => $method->isStatic(),
                'final'      => $method->isFinal(),
                'return'     => $return,
                'class'      => $method->getDeclaringClass()->name
            ];
        }

        return $ret ?? null;
    }

    /**
     * Get method names.
     * @param  string|object $class
     * @param  bool          $all
     * @return ?array
     */
    public static final function getMethodNames($class, bool $all = true): ?array
    {
        $ref = self::getReflection($class);

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        foreach ($ref->getMethods($filter) as $method) {
            $ret[] = $method->name;
        }

        return $ret ?? null;
    }
}
