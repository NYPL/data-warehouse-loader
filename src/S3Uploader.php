<?php
namespace NYPL\WarehouseExporter;

use Aws\S3\S3Client;

class S3Uploader
{
    /**
     * @var S3Client
     */
    protected static $s3Client;

    /**
     * @throws \InvalidArgumentException
     *
     * @return S3Client
     */
    public static function getS3Client()
    {
        if (!self::$s3Client) {
            self::setS3Client(
                new S3Client([
                    'version' => 'latest',
                    'region'  => Config::get('AWS_DEFAULT_REGION'),
                ])
            );
        }

        return self::$s3Client;
    }

    /**
     * @param S3Client $s3Client
     */
    public static function setS3Client($s3Client)
    {
        self::$s3Client = $s3Client;
    }

    /**
     * @param string $key
     * @param string $filename
     *
     * @throws \InvalidArgumentException
     */
    public static function uploadFile($key = '', $filename = '')
    {
        self::getS3Client()->putObject([
            'Bucket' => Config::get('S3_BUCKET_NAME'),
            'Key' => $key,
            'SourceFile' => $filename
        ]);
    }
}
