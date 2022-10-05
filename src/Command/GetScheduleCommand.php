<?php

namespace App\Command;

use App\Service\LineHandler;
use App\Service\StopHandler;
use App\Service\StopTimeHandler;
use App\Service\TripHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use ZipArchive;

#[AsCommand(
    name: 'app:get-schedule',
    description: 'Scrapping schedule from https://www.ztm.poznan.pl/pl/dla-deweloperow/gtfsFiles',
)]
class GetScheduleCommand extends Command
{
    public function __construct(
        private KernelInterface $appKernel,
        private StopHandler $stopHandler,
        private LineHandler $lineHandler,
        private TripHandler $tripHandler,
        private StopTimeHandler $stopTimeHandler
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('upload', null, InputOption::VALUE_NONE, 'Grab new files')
             ->addOption('addStops', null, InputOption::VALUE_NONE, 'Populate Stops')
             ->addOption('addLines', null, InputOption::VALUE_NONE, 'Populate Lines')
             ->addOption('addTrips', null, InputOption::VALUE_NONE, 'Populate Trips')
             ->addOption('addStopTimes', null, InputOption::VALUE_NONE, 'Populate Stop Times')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filePath = $this->appKernel->getProjectDir().'/archive/';

        if ($input->getOption('upload')) {
            $io->note('Uploading from ZTM');

            $fileName = '1.zip';
            if('zip' === pathinfo('https://www.ztm.poznan.pl/pl/dla-deweloperow/getGTFSFile/?file=20220917_20220930.zip',PATHINFO_EXTENSION))
            copy('https://www.ztm.poznan.pl/pl/dla-deweloperow/getGTFSFile/?file=20220917_20220930.zip', $filePath . $fileName);

            $zip = new ZipArchive();

            if (true === $zip->open($filePath . $fileName)) {
                $zip->extractTo($filePath);
                $zip->close();
                unlink($filePath . $fileName);
                $io->success('');
            }
        }

        if ($input->getOption('addStops')) {
            if (copy($filePath . 'stops.txt', $filePath . 'stops.csv'))
            $this->stopHandler->populate($filePath);
        }

        if ($input->getOption('addLines')) {
            $this->lineHandler->populate($filePath);
        }

        if ($input->getOption('addTrips')) {
            if (copy($filePath . 'trips.txt', $filePath . 'trips.csv')) {
                $this->tripHandler->populate($filePath);
            }
        }

        if ($input->getOption('addStopTimes')) {
            $io->note('Stop Times will take some time');
            copy($filePath . 'stop_times.txt', $filePath . 'stop_times_x.csv');

            $populateNotFinished = $this->stopTimeHandler->populate($filePath);
            while ($populateNotFinished) {
                $populateNotFinished = $this->stopTimeHandler->populate($filePath, $populateNotFinished);
            }
        }

        $io->note('Executed with ' . implode(',',$input->getOptions()));

        return Command::SUCCESS;
    }
}
