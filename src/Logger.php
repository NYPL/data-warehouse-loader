<?php
namespace NYPL\WarehouseExporter;

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Monolog\Handler\StreamHandler;

class Logger
{
    /**
     * @var \Monolog\Logger
     */
    public static $logger;

    /**
     * @return \Monolog\Logger
     */
    public static function log()
    {
        if (!self::$logger) {
            $logger = new \Monolog\Logger('log');

            $handler = new StreamHandler('php://stdout');
            $handler->setFormatter(new ColoredLineFormatter());

            $logger->pushHandler($handler);

            self::setLogger($logger);
        }

        return self::$logger;
    }

    /**
     * @param \Monolog\Logger $logger
     */
    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }
}
