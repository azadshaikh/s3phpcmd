<?php

namespace Azadshaikh\S3phpCmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use League\Flysystem\Visibility;
use \League\Flysystem\FileAttributes;
use \League\Flysystem\DirectoryAttributes;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;


class SourceToDestination extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    protected static $defaultName = 'sourcetodestination';

    /**
     * The command description shown when running "php bin/demo list".
     *
     * @var string
     */
    protected static $defaultDescription = 'Move all Files from Source to Destination S3 Bucket';

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
        $io->title('Move all Files from Source to Destination S3 Bucket');

        try {
            $sourceFilesystem = sourceS3Connect();
            $destinationFilesystem = destinationS3Connect();
            $listing = $sourceFilesystem->listContents('/', true);
            foreach ($listing as $item) {
                if ($item instanceof FileAttributes) {
                    // Code for Files
                    $path = $item->path();
                    
                    try {
                        #Get Files from S3
                        $response = $sourceFilesystem->read($path);
                    } catch (FilesystemException | UnableToReadFile $exception) {
                        $io->error($exception->getMessage());
                    }

                    try {
                        $destinationFilesystem->write($path, $response);
                        $io->info($path . '=> Moved Successfully');
                    } catch (FilesystemException | UnableToWriteFile $exception) {
                        $io->error($exception->getMessage());
                    }
                    // die();

                } elseif ($item instanceof DirectoryAttributes) {
                    // Code for Folder
                    // $path = $item->path();
                    // $io->text($path);
                }
            }
        } catch (FilesystemException $exception) {
            $io->error($exception->getMessage());
        }

        $io->success('Successfully Backup All Files From Source S3 Bucket to Backup Folder in Root Directory');
        return Command::SUCCESS;
    }
}
