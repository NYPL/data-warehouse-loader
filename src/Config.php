<?php
namespace NYPL\WarehouseExporter;

use Dotenv\Dotenv;

class Config
{
    public static $initialized = false;

    /**
     * @param string $key
     *
     * @return array|false|string
     */
    public static function get($key = '')
    {
        if (!self::isInitialized()) {
            self::initialize();
        }

        return getenv($key);
    }

    protected static function initialize()
    {
        $dotEnv = new Dotenv(__DIR__ . '/..');
        $dotEnv->load();

        self::setInitialized(true);
    }

    /**
     * @return bool
     */
    public static function isInitialized()
    {
        return self::$initialized;
    }

    /**
     * @param bool $initialized
     */
    public static function setInitialized($initialized)
    {
        self::$initialized = $initialized;
    }
}
