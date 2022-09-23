<?php

namespace App\Service;

use App\Entity\Line;
use App\Entity\Stop;
use App\Entity\Departure;
use App\Entity\Route;
use Doctrine\ORM\EntityManagerInterface;

class RouteHandler
{
    const SOURCE_NAME = 'trips.txt';
    const DEPARTURE_SOURCE_NAME = 'stop_times.txt';
    const BATCH_SIZE = 20;

    public function __construct(private EntityManagerInterface $entityManager) {

    }

    public function populate(?string $path = '') {

        $lineRepository = $this->entityManager->getRepository(Line::class);
        $stopRepository = $this->entityManager->getRepository(Stop::class);

        $fileData = function($filePath) {
            $file = fopen($filePath, 'r') ;
            if (!$file) {
                // todo throw exception
                return;
            }
            //Ignore the first line
            fgets($file);
            while (($line = fgets($file)) !== false) {
                yield explode(',', $line);
            }

            fclose($file);
        };

        $routes = $fileData($path . self::SOURCE_NAME);
        $departures = $fileData($path . self::DEPARTURE_SOURCE_NAME);

        foreach ($routes as $routeData) {
            /**
             * $data structure
             * [ 0 => route_id, 1 => service_id, 2 => trip_id, 3 => trip_headsign, 4 => direction_id, shape_id,wheelchair_accessible,brigade ]
             * $departureDataStructure
             * [ 0 => 'trip_id', 1 => 'arrival_time', 2 => 'departure_time', 3 => 'stop_id', 4 => 'stop_sequence', 5 => 'stop_headsign', 6 => 'pickup_type', 7 => 'drop_off_type'
             */
            $data = $routeData;

            // Fulfill route data from `trips.txt`
            $departureData = $departures->current();
            $route = new Route();
            $route->setDirection(boolval($data[4]));
            $route->setSystemName($data[2]);
            $route->setDay($data[1]);
            $route->setDescription($data[5]);
            $line = $lineRepository->findOneBy(['name' => $data[0]]);
            $route->setLine($line);
            while ($routeData[0] == $data[2]) {
                // setStartTime
                if($departureData[4] == 1) {
                    $startTime = new $departureData[2];
                }
                // todo skip rote for same `service_id` and `trip_headsign`
                $stop = $stopRepository->findOneBy(['systemName' => $departureData[3]]);
                $route->setStop($stop);
                // todo count with date objects
                $route->setInterval($departureData[2] - $startTime);
                $this->entityManager->persist($route);
                if($departureData[4] == 1) {
                    $departure = new Departure();
                    $departure->setRoute($route);
                    $departure->setStartAt($departureData[2]);
                    $departure->setStop($stop);
                    $this->entityManager->persist($departure);
                }
                $this->entityManager->flush();
                $this->entityManager->clear();
                $departures->next();
            }
        }
    }
}