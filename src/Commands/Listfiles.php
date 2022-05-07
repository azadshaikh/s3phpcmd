<?php

namespace Azadshaikh\S3phpCmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use \League\Flysystem\FileAttributes;
use \League\Flysystem\DirectoryAttributes;
use League\Flysystem\FilesystemException;


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

    protected function configure(): void
    {
        $this
            // ...
            ->addArgument('bucket', InputArgument::OPTIONAL, 'Choose which bucket to list files from.')
        ;
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
        $io->title('Listing all Objects in S3 Bucket');
        $S3Source = $input->getArgument('bucket');
        // $io->text($S3Source); die();

        try {
            if ($S3Source == "destination") {
                $filesystem = destinationS3Connect();
            } else {
                $filesystem = sourceS3Connect();
            }
            $listing = $filesystem->listContents('/', true);
            // print_r($listing); die();
            foreach ($listing as $item) {
                if ($item instanceof FileAttributes) {
                    // var_dump($item); die();
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
