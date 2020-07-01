<?php
declare(strict_types=1);

namespace App\UI\Command;

use Aws\S3\S3Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MinioTestCommand extends Command
{
    public function __construct()
    {
        parent::__construct('app:minio-test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Minio Test Command");

        $bucketName = 'testbucket';
        $keyName = 'testkey.txt';

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'endpoint' => 'http://minio:9000',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => 'minioadmin',
                'secret' => 'minioadmin',
            ],
        ]);

        $buckets = $s3->listBuckets()->get('Buckets');

        if (!$this->ifBucketExists($buckets, $bucketName)) {
            echo sprintf("Bucket: %s doesn't exists.\n", $bucketName);
            echo sprintf("Crating bucket: %s\n", $bucketName);
            $s3->createBucket([
                'Bucket' => $bucketName,
            ]);
        }

        $objects = $s3->listObjects([
            'Bucket' => $bucketName,
        ])->get('Contents') ?? [];


        if (!$this->ifKeyExists($objects, $keyName)) {
            echo sprintf("Key: %s doesn't exists.\n", $keyName);
            echo sprintf("Crating key: %s\n", $keyName);
            $insert = $s3->putObject([
                'Bucket' => $bucketName,
                'Key'    => $keyName,
                'Body'   => 'Hello from MinIO!!'
            ]);
        }

        $retrive = $s3->getObject([
            'Bucket' => $bucketName,
            'Key'    => $keyName,
//            'SaveAs' => 'testkey.txt'
        ]);

        echo $retrive['Body'] . PHP_EOL;

        return Command::SUCCESS;
    }

    private function ifBucketExists(array $buckets, string $bucketName): bool
    {
        foreach ($buckets as $bucket) {
            if ($bucket['Name'] === $bucketName) {
                return true;
            }
        }
        return false;
    }

    private function ifKeyExists(array $objects, string $keyName): bool
    {
        foreach ($objects as $object) {
            if ($object['Key'] === $keyName) {
                return true;
            }
        }
        return false;
    }

}