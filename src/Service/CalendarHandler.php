<?php

namespace App\Service;

use App\Entity\Calendar;
use App\Service\CalendarHelper;
use Doctrine\ORM\EntityManagerInterface;

class CalendarHandler
{
    const SOURCE_NAME = 'calendar.txt';

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
        $calendarRecord = new Calendar();
        foreach ($fileData() as $line) {
            /**
             * $data structure
             * [0 => 'service_id', 1 => 'monday', 2 => 'tuesday', 3 =>'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => sunday, 8 => 'start_date', 9 => 'end_day'];
             */
            $data = explode(',', $line);
            $calendarRecord = $this->setDates($calendarRecord, $data);
            $dateNum = array_shift($data);
            foreach ($data as $key => $value) {
                if(in_array($key, range(0, 6))) {
                    if ($value == 1) {
                        $calendarRecord->{'set' . jddayofweek($key,CAL_JULIAN)}($dateNum);
                    }
                }
            }
        }
        $this->entityManager->persist($calendarRecord);
        $this->entityManager->flush();
    }

    private function setDates(Calendar $calendar, array $data): Calendar {
        if (empty($calendar->getEndAt()) || empty($calendar->getStartAt())) {
            $calendar->setStartAt(CalendarHelper::getDateFromString($data[8]))
                     ->setEndAt(CalendarHelper::getDateFromString($data[9]));
        }
        return $calendar;
    }
}