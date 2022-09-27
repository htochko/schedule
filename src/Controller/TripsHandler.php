<?php
namespace App\Controller;

use App\Entity\Stop;
use App\Entity\Line;
use App\Repository\StopRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

#[AsController]
class TripsHandler extends AbstractController
{
    public function __construct(private StopRepository $stopRepository) {

    }

    #[Route(
        name: 'trips',
        path: '/stops/{id}/trips',
        methods: ['GET'],
        defaults: [
        '_api_resource_class' => Stops::class,
        '_api_operation_name' => '_api_/stops/{id}/trips_get',
    ],
    )]
    /**
     * @example [{'lineName' => ['5min', '8min', '9min']}, {...}]
     */
    public function __invoke(EntityManagerInterface $entityManager, int $id) {
        // temporary fake method
        $qb = $entityManager->createQueryBuilder()
            ->select('l.id', 'l.name')
            ->from(Line::class, 'l')
            ->addSelect( "DATE_ADD(CURRENT_TIMESTAMP(),5,'minute') as departure_at")
            ->setMaxResults( 10 );
        return $qb->getQuery()->getResult();
    }
}