<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\StopTime;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\NoReturn;

class StopTimeExtension implements QueryCollectionExtensionInterface
{
    #[NoReturn] public function __construct()
    {
    }

    private function addWhere(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(sprintf('%s.departure_at > :now', $rootAlias))
            ->setParameter('now', 'now');
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($resourceClass != StopTime::class) {
            return;
        }

        if (array_key_exists('filters', $context)) {

            if (array_key_exists('stop.id', $context['filters'])) {
                $this->addWhere($queryBuilder, $resourceClass);
            }

        }
    }
}