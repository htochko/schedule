<?php

namespace App\Repository;

use App\Entity\Line;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Line>
 *
 * @method Line|null find($id, $lockMode = null, $lockVersion = null)
 * @method Line|null findOneBy(array $criteria, array $orderBy = null)
 * @method Line[]    findAll()
 * @method Line[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Line::class);
    }

    public function add(Line $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Line $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
