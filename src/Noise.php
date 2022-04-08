<?php

// include vendor from folder up
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

// The internal adapter
$adapter = new League\Flysystem\Local\LocalFilesystemAdapter(
    // Determine root directory
    dirname(dirname(__FILE__))
);

// The FilesystemOperator
$filesystem = new League\Flysystem\Filesystem($adapter);

$path = dirname(dirname(__FILE__));
// echo $path;
// die();
$recursive = true;



// try {
//     $fileone = 'azad.txt';
//     echo $fileone;
//     // die();
//     $filesystem->write($fileone, 'azad shaikh .com', ['visibility' => 'public']);
//     die();
// } catch (FilesystemException | UnableToWriteFile $exception) {
//     // handle the error
// }


try {
    $listing = $filesystem->listContents('/src', true);

 
    /** @var \League\Flysystem\StorageAttributes $item */
    foreach ($listing as $item) {
        $path = $item->path();

        echo $path;

        if ($item instanceof \League\Flysystem\FileAttributes) {
        } elseif ($item instanceof \League\Flysystem\DirectoryAttributes) {
            var_dump($item);
        }
    }
} catch (FilesystemException $exception) {
    echo 'not able to list file and directory';
}
