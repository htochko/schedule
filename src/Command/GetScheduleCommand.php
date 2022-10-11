<?php

namespace App\Command;

use App\Repository\CalendarRepository;
use App\Service\CalendarHandler;
use App\Service\CalendarHelper;
use App\Service\LineHandler;
use App\Service\StopHandler;
use App\Service\StopTimeHandler;
use App\Service\TripHandler;
use Goutte\Client;
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
    const ENDPOINT_ZTM_POZNAN = 'https://www.ztm.poznan.pl/pl/dla-deweloperow/gtfsFiles';

    public function __construct(
        private KernelInterface $appKernel,
        private StopHandler $stopHandler,
        private LineHandler $lineHandler,
        private TripHandler $tripHandler,
        private StopTimeHandler $stopTimeHandler,
        private CalendarHandler $calendarHandler,
        private CalendarHelper $calendarHelper,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('checkNewLink', null, InputOption::VALUE_NONE, 'Finding link to last resource')
             ->addOption('upload', null, InputOption::VALUE_NONE, 'Grab new files')
             ->addOption('addStops', null, InputOption::VALUE_NONE, 'Populate Stops')
             ->addOption('addLines', null, InputOption::VALUE_NONE, 'Populate Lines')
             ->addOption('addTrips', null, InputOption::VALUE_NONE, 'Populate Trips')
             ->addOption('addStopTimes', null, InputOption::VALUE_NONE, 'Populate Stop Times')
             ->addOption('addCalendar', null, InputOption::VALUE_NONE, 'Fill calendar')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filePath = $this->appKernel->getProjectDir().'/archive/';

        if ($input->getOption('checkNewLink')) {
            $io->note('finding a link');
            try {
                $link = $this->getLinkName();
                $parts = parse_url($link);
                parse_str($parts['query'], $query);
                $fileName = $query['file'];
                $io->note("New file link is $fileName");
                $io->note($this->calendarHelper->checkLastFileScrapped($fileName));
            } catch (\Exception $exception) {
                $io->error($exception->getMessage());
                return 1;
            }
        }

        if ($input->getOption('upload')) {
            $io->note('Uploading from ZTM');
            $remoteFilePath = $this->getLinkName();
            $fileName = '1.zip';
            if('zip' === pathinfo($remoteFilePath,PATHINFO_EXTENSION))
            copy($remoteFilePath, $filePath . $fileName);

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

        if ($input->getOption('addCalendar')) {
            $this->calendarHandler->populate($filePath);
        }

        // todo add clear data of execution from $input->getOptions()
        $io->note('Executed');

        return Command::SUCCESS;
    }

    private function getLinkName(): string
    {
        $client = new Client();
        $crawler = $client->request('GET', self::ENDPOINT_ZTM_POZNAN);
        $link = $crawler->filter('table')
            ->last()->filter('tbody')->filter('tr')->first()->children()->last()->filter('a');

        return $link->getBaseHref() . $link->attr('href');
    }
}
