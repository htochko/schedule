<?php

namespace App\Service;

use App\Repository\CalendarRepository;

class CalendarHelper
{
    public function __construct(private CalendarRepository $calendarRepository)
    {
    }

    public static function getDateFromString(string $data): \DateTimeImmutable{
        $date = new \DateTimeImmutable();
        return $date->setDate(substr($data, 0, 4), substr($data, 4, 2), substr($data, 6, 2))
                    ->setTime(0,0, 0, 0);
    }

    public function checkLastFileScrapped($newFileName): string
    {
        $newStart = self::getDateFromString(substr($newFileName, 0, 8))->format('d M y');
        $newEnd = self::getDateFromString(substr($newFileName, 9, 8))->format('d M y');
        $lastSynced = $this->calendarRepository->getLastSyncedRecord();
        $start = $lastSynced->getStartAt()->format('D, d M y');
        $end = $lastSynced->getEndAt()->format('D, d M y');
        if ($lastSynced->getEndAt() === $newEnd && $lastSynced->getEndAt() === $newStart) {
            return "Already on the newest version: from $newStart to $newEnd";
        }
        return "the newest version: from $newStart to $newEnd, last synced: from $start to $end";
    }
}