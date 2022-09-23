<?php

namespace App\Service;

use App\Entity\Line;
use Doctrine\ORM\EntityManagerInterface;

class LineHandler
{
    const SOURCE_NAME = 'routes.txt';
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
            fgets($file);
            while (($line = fgets($file)) !== false) {
                yield $line;
            }

            fclose($file);
        };
        $i = 1;

        foreach ($fileData() as $line) {
            /**
             * $data structure
             * [0 => 'stop_id', 1 => 'stop_code', 2 => 'stop_name', 3 =>'stop_lat', 4 => 'stop_lon', 5 => 'zone_id'];
             */
            $data = explode(',', $line);
            $theLine = new Line();
            $theLine->setName($data[0]);
            $this->entityManager->persist($theLine);
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