<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
class StopTimeHandlerWithFileReduction
{
    // 'stop_times_x.txt';
    // ;
    const SOURCE_NAME = 'stop_times_x.txt';
    const COPY_NAME = 'tmp_stop_times.txt';
    const BROKEN = 'broken.txt';
    const BATCH_SIZE = 80;

    private bool $fileMode = false;
    private $file = null;
    private array $stops = [];
    private ?int $trip_id = null;
    private ?string $trip = null;
    private ?string $route = '';

    public function __construct(
        private Connection $connection
    ) {
        echo (memory_get_usage() . '\n');
    }

    public function populate(?string $path = '', ?bool $continue = false) {
        $i = 1;

        $fileData = function() use ($path, $continue){
            $filePath = $path . self::SOURCE_NAME;
            $file = fopen($filePath, 'r') ;
            if (!$file) {
                return;
            }
            //Ignore the first line\
            if (false === $continue) {
                fgets($file);
            }
            while (($line = fgets($file)) !== false) {
                yield $line;
            }
            fclose($file);
            $this->replaceFiles($path);
        };
        $arrayData = [];
        foreach ($fileData() as $line) {
            $i++;
            if ($this->fileMode) {
                fwrite($this->file, $line);
            } else {
            /**
             * $data structure
             * [0 => trip_id, 1 => arrival_time, 2 => departure_time, 3 => stop_id, 4 => stop_sequence, 5 => stop_headsign,pickup_type,drop_off_type];
             */
            $data = explode(',', $line);
            $this->checkRouteChanged($data[4]);
            $trip_id = $this->getTripIdBySystemName($data[0]);
            $stop_id = $this->getStopIdBySystemName(intval($data[3]));
            if ($stop_id && intval(substr($data[2], 0, 2)) < 24) {
                $arrayData[] = [
                    'trip_id' => $trip_id,
                    'stop_id' => $stop_id,
                    'sequence' => intval($data[4]),
                    'departure_at' => $data[2]
                ];
            } else {
                $broken = fopen($path . self::BROKEN, 'w');
                fwrite($broken, $line);
                fclose($broken);
            }

            if (($i % self::BATCH_SIZE) === 0) {
                $this->bulkInsert($arrayData);
                $arrayData = [];
                if (!gc_enabled()) {
                    gc_enable();
                }
                gc_collect_cycles();
                if(memory_get_usage() >= 134217728 * 0.8) {
                    $this->startFileMode($path);
                }
            }
        }
        }

        if (!empty($arrayData)) {
            $this->bulkInsert($arrayData);
        }
    }

    private function replaceFiles($path) {
        if (!empty($this->file)) {
            fclose($this->file);
            rename($path . self::COPY_NAME,$path . self::SOURCE_NAME);
        }
    }

    private function startFileMode(string $filePath): void
    {
        $this->fileMode = true;
        $this->file = fopen($filePath . self::COPY_NAME, "w");
    }

    /**
     * Finds trip's id, keeps for next queries
     */
    private function getTripIdBySystemName(string $systemName): int {
        if ($this->trip === $systemName) {
            return $this->trip_id;
        }
        $this->trip_id = $this->connection->fetchOne('SELECT id FROM trip WHERE system_name = ?', [$systemName]);
        $this->trip = $systemName;
        return $this->trip_id;
    }

    /**
     * Finds stop's id, collects data for next queries
     */
    private function getStopIdBySystemName(int $systemName): int {
        if (array_key_exists($systemName, $this->stops)) {
            return $this->stops[$systemName];
        }
        $stop = $this->connection->fetchOne('SELECT id from stop WHERE system_name = ?', [$systemName]);
        if ($stop) {
            $this->stops[$systemName] = $stop;
        }
        return $stop;
    }

    /**
     * Bulk insert to avoid memory leaks
     */
    private function bulkInsert($stopTimes): void
    {
        $placeholders = [];
        $values = [];
        $types = [];

        foreach ($stopTimes as $value) {
            $placeholders[] = '(?)';
            $values[] = array_values($value);
            $types[] = Connection::PARAM_INT_ARRAY;
        }

        $this->connection->executeStatement(
            'INSERT INTO "stop_time" ("trip_id", "stop_id", "sequence", "departure_at")  VALUES ' . implode(', ', $placeholders),
            $values,
            $types
        );
    }

    /**
     * resets stops array for new route
     */
    private function checkRouteChanged(string $name):void {
        if ($name !== $this->route) {
            $this->route = $name;
            $this->stops = [];
        }
    }
}