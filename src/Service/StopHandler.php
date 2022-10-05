<?php

namespace App\Service;

use App\Entity\Stop;
use Doctrine\ORM\EntityManagerInterface;

class StopHandler
{
    const SOURCE_NAME = 'stops.csv';
    const BATCH_SIZE = 20;

    public function __construct(private EntityManagerInterface $entityManager) {

    }

    public function populate(?string $path = '') {
        $filePath = $path . self::SOURCE_NAME;
        $fileData = function() use ($filePath){
            $file = fopen($filePath, 'r') ;
            if (!$file) {
                return;
            }
            //Ignore the first line
            fgetcsv($file);
            while (($line = fgetcsv($file)) !== false) {
                yield $line;
            }

            fclose($file);
        };
        $i = 1;

        foreach ($fileData() as $data) {
            /**
             * $data structure
             * [0 => 'stop_id', 1 => 'stop_code', 2 => 'stop_name', 3 =>'stop_lat', 4 => 'stop_lon', 5 => 'zone_id'];
             */
            $stop = new Stop();
            $stop->setSystemName($data[0])
                ->setCode($data[1])
                ->setName($data[2])
                ->setLon($data[3])
                ->setLat($data[4]);
            $this->entityManager->persist($stop);
            $i++;
            if (($i % self::BATCH_SIZE) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine!
            }
        }

        $this->entityManager->flush(); // Persist objects that did not make up an entire batch
        $this->entityManager->clear();
    }
}