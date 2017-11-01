<?php
namespace NYPL\WarehouseExporter;

use Aura\Sql\ExtendedPdo;

class WarehouseWriter
{
    /**
     * @var ExtendedPdo
     */
    public static $pdo;

    /**
     * @param string $s3Prefix
     *
     * @return \PDOStatement
     */
    public static function copyRecords($s3Prefix = '')
    {
        $statement = 'copy circ_trans 
        from ' . self::getPdo()->quote('s3://' . Config::get('S3_BUCKET_NAME') . '/' . $s3Prefix) . '
        iam_role ' . self::getPdo()->quote(Config::get('DW_ARN')) . '
        region ' . self::getPdo()->quote(Config::get('AWS_DEFAULT_REGION')) . '
        format as avro ' . self::getPdo()->quote('auto');

        return self::getPdo()->fetchValue($statement);
    }

    /**
     * @return ExtendedPdo
     */
    public static function getPdo()
    {
        if (!self::$pdo) {
            self::setPdo(
                new ExtendedPdo(
                    Config::get('DW_DSN'),
                    Config::get('DW_USERNAME'),
                    Config::get('DW_PASSWORD'),
                    [],
                    []
                )
            );
        }

        return self::$pdo;
    }

    /**
     * @param ExtendedPdo $pdo
     */
    public static function setPdo($pdo)
    {
        self::$pdo = $pdo;
    }
}
