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
use League\Flysystem\UnableToSetVisibility;


class VisibilityPublic extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    protected static $defaultName = 'visibilitypublic';

    /**
     * The command description shown when running "php bin/demo list".
     *
     * @var string
     */
    protected static $defaultDescription = 'It Will Set Visibility of all Files in S3 to PUBLIC';

    protected function configure(): void
    {
        $this
            // ...
            ->addArgument('bucket', InputArgument::OPTIONAL, 'Choose bucket to perform action on.')
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
        $io->title('Setting Visibility to PUBLIC for all Objects in S3 Bucket');
        $S3Source = $input->getArgument('bucket');

        try {
            if ($S3Source == "destination") {
                $filesystem = destinationS3Connect();
            } else {
                $filesystem = sourceS3Connect();
            }
            $listing = $filesystem->listContents('/', true);
            foreach ($listing as $item) {
                if ($item instanceof FileAttributes) {
                    // Code for Files
                    $path = $item->path();

                    //Set Visibility To Public
                    try {

                        if ($filesystem->visibility($path) === 'private') {
                            $filesystem->setVisibility($path, 'public');
                            $io->info($path . '=> Done');
                        } else {
                            $io->text($path . '=> Already Public');
                        }

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

        $io->success('Successfully Set Visibility to PUBLIC for all Objects in S3 Bucket');
        return Command::SUCCESS;
    }
}
