<?php
declare(strict_types=1);

namespace arrays;

/**
 * @package arrays
 * @object  arrays\Autoload
 * @author  Kerem Güneş <k-gun@mail.com>
 */
final class Autoload
{
    /**
     * Instance.
     * @var arrays\Autoload
     */
    private static $instance;

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
     * @throws \RuntimeException
     */
    public function register(): bool
    {
        return spl_autoload_register(function($object) {
            if ($object[0] != '\\') {
                $object = '\\'. $object;
            }

            $ns  = '\arrays';
            $dir = __dir__;

            // arrays objects only
            if (strpos($object, $ns) !== 0) {
                return;
            }

            $object = substr($object, strlen($ns) + 1);
            $objectFile = strtr("{$dir}/{$object}.php", ['\\' => '/']);

            if (!file_exists($objectFile)) {
                throw new \RuntimeException("Object file '{$objectFile}' not found");
            }

            require $objectFile;
        });
    }
}

// shorcut for require
return Autoload::init();
