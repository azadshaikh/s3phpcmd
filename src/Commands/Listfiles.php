<?php

namespace Azadshaikh\S3phpCmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
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
    protected static $defaultDescription = 'It Will List all Files in S3';

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

        try {
            $filesystem = destinationS3Connect();
            $listing = $filesystem->listContents('/', true);
            foreach ($listing as $item) {
                if ($item instanceof FileAttributes) {
                    // Code for Files
                    $path = $item->path();
                    $io->text('File: ' . $path);
                    //Set Visibility To Public
                } elseif ($item instanceof DirectoryAttributes) {
                    // Code for Folder
                    $io->text('Folder: ' . $path);
                }
            }
        } catch (FilesystemException $exception) {
            $io->error($exception->getMessage());
        }

        $io->success('Successfully listed all objects in S3 Bucket');
        return Command::SUCCESS;
    }
}
