<?php

namespace Azadshaikh\S3phpCmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

use League\Flysystem\Filesystem;
use \League\Flysystem\FileAttributes;
use \League\Flysystem\DirectoryAttributes;


class Listfiles extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    protected static $defaultName = 'listfiles';

    /**
     * The command description shown when running "php bin/demo list".
     *
     * @var string
     */
    protected static $defaultDescription = 'It Will List Files in S3';

    /**
     * Execute the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output); //https://symfony.com/doc/current/console/style.html
        $io->title('Listing all Objects in S3 Bucket');

        $options = [
            'version' => 'latest',
            'region'  => 'us-southeast-1',
            'endpoint' => 'https://us-southeast-1.linodeobjects.com',
            'use_path_style_endpoint' => (bool)'true',
            'credentials' => [
                'key'    => 'VWK71HJ5OVD5YTKALX8K',
                'secret' => 'fMmRQQDiBS5bZnqbDEFqFUAfg8WjbIDkD1vFBpy6',]];

        /** @var Aws\S3\S3ClientInterface $client */
        $client = new S3Client($options);

        // The internal adapter
        $adapter = new AwsS3V3Adapter(
            // S3Client
            $client,
            // Bucket name
            'baseapp',
            // Optional path prefix
            '',
            // Visibility converter (League\Flysystem\AwsS3V3\VisibilityConverter)
            new PortableVisibilityConverter(
                // Optional default for directories
                Visibility::PUBLIC // or ::PRIVATE
            )
        );

        // The FilesystemOperator
        $filesystem = new Filesystem($adapter);

        try {
            $listing = $filesystem->listContents('/', true);

            /** @var \League\Flysystem\StorageAttributes $item */
            foreach ($listing as $item) {
                if ($item instanceof FileAttributes) {
                    // Code for Files
                    $path = $item->path();
                   

                    /* //Check Visibility
                    try {
                        $visibility = $filesystem->visibility($path);
                        $io->text($visibility);
                    } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
                        $io->error($exception->getMessage());
                    }
                    */

                    //Set Visibility To Public
                    try {
                        $filesystem->setVisibility($path, 'public');
                        $io->text($path.'=> Done');
                        // $io->info('File Visibility Set to Public');
                    } catch (FilesystemException | UnableToSetVisibility $exception) {
                        $io->error($exception->getMessage());
                    }


                } elseif ($item instanceof DirectoryAttributes) {
                    // Code for Folder
                    // $path = $item->path();
                    // $io->text($path);
                }
            }
        } catch (FilesystemException $exception) {
            $io->error($exception->getMessage());
        }

        $io->success('Successfully listed all objects in S3 Bucket');
        return Command::SUCCESS;
    }
}
