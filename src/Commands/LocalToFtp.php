<?php

namespace Azadshaikh\S3phpCmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use League\Flysystem\Visibility;
use League\Flysystem\FileAttributes;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;

class LocalToFtp extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    protected static $defaultName = 'localtoftp';

    /**
     * The command description shown when running "php bin/demo list".
     *
     * @var string
     */
    protected static $defaultDescription = 'Move all Files from Local (files in /backup folder) to FTP';

    protected function configure(): void
    {
        $this
            // ...
            ->addArgument('bucket', InputArgument::OPTIONAL, 'Choose bucket to perform action on.');
    }

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
        $io->title('Move all Files from Local Backup Folder to FTP');

        $S3BucketName = $input->getArgument('bucket');

        // var_dump($S3BucketName);
        // die();

        try {
            if ($S3BucketName == "destination") {
                $S3Bucket = destinationS3Connect();
            } else {
                $S3Bucket = ftpConnect();
            }
            $localfilesystem = localFileConnect('backup');

            $listing = $localfilesystem->listContents('/', true);
            foreach ($listing as $item) {
                if ($item instanceof FileAttributes) {
                    // Code for Files
                    $path = $item->path();

                    try {
                        #Get Files from Local
                        $response = $localfilesystem->read($path);
                    } catch (FilesystemException | UnableToReadFile $exception) {
                        $io->error($exception->getMessage());
                    }

                    try {

                        // $mimeType = $S3Bucket->mimeType($path);
                        $FileExtension = pathinfo($path, PATHINFO_EXTENSION);
                        // echo $FileExtension . "\n";
                        // var_dump($FileExtension);
                        $NotAllowed = array('php', 'phtml', 'html'); //skip this file extension
                        if (!in_array($FileExtension, $NotAllowed)) {
                            $S3Bucket->write($path, $response);
                            // $S3Bucket->setVisibility($path, 'public');
                            $io->info($path . ' => Moved Successfully and Set Visibility to Public');
                        }
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

        $io->success('Successfully Moved All Files From Backup Folder in Root Directory to S3 Bucket');
        return Command::SUCCESS;
    }
}
