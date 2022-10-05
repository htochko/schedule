<?php

namespace App\Command;

use App\Service\StopTimeHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;


#[AsCommand(
    name: 'app:get-stop-times',
    description: 'Scrapping schedule from https://www.ztm.poznan.pl/pl/dla-deweloperow/gtfsFiles',
)]
class GetStopTimesCommand extends Command
{
    public function __construct(
        private KernelInterface $appKernel,
        private StopTimeHandler $stopTimeHandler
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('continue', null, InputOption::VALUE_NONE, 'continue add stoptimes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        ini_set('memory_limit', '512M');
        $filePath = $this->appKernel->getProjectDir().'/archive/';
        copy($filePath . 'stop_times.txt', $filePath . 'stop_times_x.csv');
        $populateNotFinished = $this->stopTimeHandler->populate($filePath);
        while ($populateNotFinished) {
            $populateNotFinished = $this->stopTimeHandler->populate($filePath, $populateNotFinished);
        }

        $io->note('Executed with ' . implode(',',$input->getOptions()));

        return Command::SUCCESS;
    }
}
