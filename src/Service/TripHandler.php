<?php

namespace App\Service;

use App\Entity\Line;
use App\Repository\LineRepository;
use Doctrine\DBAL\Connection;

class TripHandler
{
    const SOURCE_NAME = 'trips.csv';
    const BATCH_SIZE = 80;

    private ?Line $line = null;

    public function __construct(
        private Connection $connection,
        private LineRepository $lineRepository) {

    }

    /**
     * Populates database with file objects
     */
    public function populate(?string $path = '') {
        $this->path = $path;
        $filePath = $this->path . self::SOURCE_NAME;
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
        $arrayData = [];
        foreach ($fileData() as $order => $itemData) {
            /**
             * $data structure
             * [0 => route_id(line), 1 => service_id, 2 => trip_id, 3 => trip_headsign, 4=> direction_id,shape_id,wheelchair_accessible,brigade]
             */
            $line_id = $this->getLineIdByName($itemData[0]);
            $arrayData[] = [
                    'line_id' => $line_id,
                    'day' => intval($itemData[1]),
                    'system_name' => $itemData[2],
                    'header' => $itemData[3],
                    'direction' => boolval($itemData[4])];
            $i++;
            if (($i % self::BATCH_SIZE) === 0) {
                $this->bulkInsert($arrayData);
                $arrayData = [];
                gc_collect_cycles();
            }
        }
        if (!empty($arrayData)) {
            $this->bulkInsert($arrayData);
        }
    }

    /**
     * Bulk insert to avoid memory leaks
     */
    private function bulkInsert($trips): void
    {
        $placeholders = [];
        $values = [];
        $types = [];

        foreach ($trips as $columnName => $value) {
            $placeholders[] = '(?)';
            $values[] = array_values($value);
            $types[] = Connection::PARAM_INT_ARRAY;
        }

        $this->connection->executeStatement(
            'INSERT INTO "trip" ("line_id", "day", "system_name", "header", "direction")  VALUES ' . implode(', ', $placeholders),
            $values,
            $types
        );
    }

    /**
     * Gets line from name and keeps until iterating file will switch to new line
     */
    private function getLineIdByName(string $name):int {
        if ($this->line instanceof Line && $this->line->getName() == $name) {
            return $this->line->getId();
        }
        $this->line = $this->lineRepository->findOneBy(['name' => $name]);
        return $this->line->getId();
    }
}