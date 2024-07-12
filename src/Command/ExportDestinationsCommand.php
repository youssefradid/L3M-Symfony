<?php

namespace App\Command;

use App\Repository\DestinationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'L3M:export-destinations',
    aliases: ['L3M:exp-dest']
)]
class ExportDestinationsCommand extends Command
{
    public function __construct(private DestinationRepository $destinationRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $expIO = new SymfonyStyle($input, $output);
        $destinations = $this->destinationRepository->findAll();

        $filename = 'destinations.csv';
        $handle = fopen($filename, 'w');
        //first line
        fputcsv($handle, ['name', 'description', 'price', 'duration','imageUrl']);

        foreach ($destinations as $destination) {
            fputcsv($handle, [
                $destination->getName(),
                $destination->getDescription(),
                $destination->getPrice(),
                $destination->getDuration(),
                $destination->getImageUrl()
            ]);
        }

        fclose($handle);

        $expIO->success('Destinations have been exported to ' . $filename);

        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this
        ->setDescription('Exports all destinations to a CSV file')
        ->setHelp('This command allows you to exports destinations!!');
    }
}
