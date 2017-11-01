<?php
namespace NYPL\WarehouseExporter;

class RecordStorage
{
    const KEY_PREFIX = 'WarehouseExporter:';

    /**
     * @var \Redis
     */
    public static $redis;


    /**
     * @return \Redis
     */
    public static function getRedis()
    {
        if (!self::$redis) {
            $redis = new \Redis();
            $redis->connect(Config::get('REDIS_HOST'));
            $redis->setOption(\Redis::OPT_PREFIX, self::KEY_PREFIX);

            self::setRedis($redis);
        }

        return self::$redis;
    }

    /**
     * @param \Redis $redis
     */
    public static function setRedis($redis)
    {
        self::$redis = $redis;
    }
}
