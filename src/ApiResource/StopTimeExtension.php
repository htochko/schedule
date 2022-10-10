<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\StopTime;
use App\Repository\CalendarRepository;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\NoReturn;

class StopTimeExtension implements QueryCollectionExtensionInterface
{
    #[NoReturn] public function __construct( private CalendarRepository $calendarRepository)
    {
    }

    private function addWhere(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(sprintf('%s.departure_at > :now', $rootAlias))
            ->setParameter('now', 'now');
    }

    private function addDefaultDay(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $tripAlias = $queryNameGenerator->generateParameterName('trip');

        $queryBuilder
            ->join(sprintf('%s.trip', $rootAlias), $tripAlias)
            ->andWhere(sprintf('%s.day = :day', $tripAlias))
            ->setParameter('day', $this->calendarRepository->getTodayDayNumber());
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($resourceClass != StopTime::class) {
            return;
        }

        if (array_key_exists('filters', $context)) {
            if (array_key_exists('stop.id', $context['filters'])) {
                $this->addWhere($queryBuilder, $queryNameGenerator);
                // do not infer default value if day is set by filter
                if (!array_key_exists('trip.day', $context['filters'])) {
                    $this->addDefaultDay($queryBuilder, $queryNameGenerator);
                }
            }
        }
    }
}