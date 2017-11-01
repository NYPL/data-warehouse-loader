<?php
namespace NYPL\WarehouseExporter;

class AvroInitializer
{
    protected static $initialized = false;

    /**
     * @return bool
     */
    protected static function isInitialized()
    {
        return self::$initialized;
    }

    /**
     * @param bool $initialized
     */
    protected static function setInitialized($initialized)
    {
        self::$initialized = $initialized;
    }

    public static function initialize()
    {
        if (!self::isInitialized()) {
            require __DIR__ . '/../vendor/apache/avro/lang/php/lib/avro.php';

            self::setInitialized(true);
        }
    }
}
