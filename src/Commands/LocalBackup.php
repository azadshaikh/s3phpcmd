<?php

namespace Azadshaikh\S3phpCmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use League\Flysystem\Visibility;
use \League\Flysystem\FileAttributes;
use \League\Flysystem\DirectoryAttributes;


class LocalBackup extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    protected static $defaultName = 'localbackup';

    /**
     * The command description shown when running "php bin/demo list".
     *
     * @var string
     */
    protected static $defaultDescription = 'It Backup All Files in backup Folder in Root Directory';

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
        $io->title('Backup All Files From Source S3 Bucket to Backup Folder in Root Directory');

        try {
            $s3filesystem = sourceS3Connect();
            $localfilesystem = localFileConnect('backup');
            $listing = $s3filesystem->listContents('/', true);
            foreach ($listing as $item) {
                if ($item instanceof FileAttributes) {
                    // Code for Files
                    $path = $item->path();
                    
                    try {
                        #Get Files from S3
                        $response = $s3filesystem->read($path);
                    } catch (FilesystemException | UnableToReadFile $exception) {
                        $io->error($exception->getMessage());
                    }

                    try {
                        $localfilesystem->write($path, $response);
                        $io->info($path . '=> Downloaded');
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
