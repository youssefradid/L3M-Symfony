<?php

namespace App\Test\Command;

use App\Entity\Destination;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DestinationRepository;
use App\Command\ExportDestinationsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ExportDestinationCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $destinationRepository = $this->createMock(DestinationRepository::class);
        
        $destination = new Destination();
        $destination->setName('Merzouga');
        $destination->setDescription('13 nights in desert');
        $destination->setPrice(2000);
        $destination->setDuration(10);
        $destination->setImageUrl('http://google.com/image.jpg');

        $destinationRepository->method('findAll')->willReturn([$destination]);
        $l3mCommand = new ExportDestinationsCommand($destinationRepository);
        $commandTester = new CommandTester($l3mCommand);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('[OK] Destinations have been exported to destinations.csv', $output);

        $filename = 'destinations.csv';
        $this->assertFileExists($filename);
        $fileContent = file_get_contents($filename);
        $expectedContent = "name,description,price,duration,imageUrl\n" .
                           "Merzouga,\"13 nights in desert\",2000,10,http://google.com/image.jpg\n";
        $this->assertEquals($expectedContent, $fileContent);
    }


}

