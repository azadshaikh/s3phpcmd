<?php   
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

use \League\Flysystem\FileAttributes;
use \League\Flysystem\DirectoryAttributes;


function localFileConnect($path) {

// The internal adapter
$adapter = new LocalFilesystemAdapter(
    // Determine root directory
    ROOT_PATH.'/'.$path,
);

// The FilesystemOperator
return $filesystem = new Filesystem($adapter);

}


function sourceS3Connect () {
    $options = [
        'version' => env('SOURCE_S3_VERSION'),
        'region'  => env('SOURCE_S3_DEFAULT_REGION'),
        'endpoint' => env('SOURCE_S3_ENDPOINT'),
        'use_path_style_endpoint' => (bool)env('SOURCE_S3_USE_PATH_STYLE_ENDPOINT'),
        'credentials' => [
            'key'    => env('SOURCE_S3_ACCESS_KEY_ID'),
            'secret' => env('SOURCE_S3_SECRET_ACCESS_KEY'),
        ]
    ];
    $client = new S3Client($options);
    // The internal adapter
    $adapter = new AwsS3V3Adapter(
        // S3Client
        $client,
        // Bucket name
        env('SOURCE_S3_BUCKET'),
        // Optional path prefix
        '',
        // Visibility converter (League\Flysystem\AwsS3V3\VisibilityConverter)
        new PortableVisibilityConverter(
            // Optional default for directories
            Visibility::PUBLIC // or ::PRIVATE
        )
    );
    // The FilesystemOperator
    return $filesystem = new Filesystem($adapter);
    }

    
function destinationS3Connect () {
    $options = [
        'version' => env('DESTINATION_S3_VERSION'),
        'region'  => env('DESTINATION_S3_DEFAULT_REGION'),
        'endpoint' => env('DESTINATION_S3_ENDPOINT'),
        'use_path_style_endpoint' => (bool)env('DESTINATION_S3_USE_PATH_STYLE_ENDPOINT'),
        'credentials' => [
            'key'    => env('DESTINATION_S3_ACCESS_KEY_ID'),
            'secret' => env('DESTINATION_S3_SECRET_ACCESS_KEY'),
        ]
    ];
    $client = new S3Client($options);
    // The internal adapter
    $adapter = new AwsS3V3Adapter(
        // S3Client
        $client,
        // Bucket name
        env('DESTINATION_S3_BUCKET'),
        // Optional path prefix
        '',
        // Visibility converter (League\Flysystem\AwsS3V3\VisibilityConverter)
        new PortableVisibilityConverter(
            // Optional default for directories
            Visibility::PUBLIC // or ::PRIVATE
        )
    );
    // The FilesystemOperator
    return $filesystem = new Filesystem($adapter);
    }