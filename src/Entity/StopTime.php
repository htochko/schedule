<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\StopTimeRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

#[ApiResource(
    normalizationContext: ['groups' => ['times:view']]
)]
#[ApiFilter(SearchFilter::class, properties: ['stop.id' => 'exact', 'trip.day' => 'exact'])]
#[ApiFilter(DateFilter::class, properties: ['departure_at' => DateFilter::PARAMETER_STRICTLY_AFTER])]
#[ApiFilter(OrderFilter::class, properties: ['departure_at' => 'ASC'])]
#[ORM\Entity(repositoryClass: StopTimeRepository::class)]
class StopTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[Groups(['stop:routes', 'times:view'])]
    #[ORM\ManyToOne(inversedBy: 'stopTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trip $trip = null;

    #[Groups(['times:view'])]
    #[ORM\ManyToOne(inversedBy: 'stopTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stop $stop = null;

    #[ORM\Column(nullable: true)]
    private ?int $sequence = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $synced_at = null;

    #[Groups(['stop:routes', 'times:view'])]
    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $departure_at = null;

    private ?string $departuresIn;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): self
    {
        $this->trip = $trip;

        return $this;
    }

    public function getStop(): ?Stop
    {
        return $this->stop;
    }

    public function setStop(?Stop $stop): self
    {
        $this->stop = $stop;

        return $this;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(?int $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }

    public function getSyncedAt(): ?\DateTimeImmutable
    {
        return $this->synced_at;
    }

    public function setSyncedAt(\DateTimeImmutable $synced_at): self
    {
        $this->synced_at = $synced_at;

        return $this;
    }

    public function getDepartureAt(): ?\DateTimeImmutable
    {
        return $this->departure_at;
    }

    #[Groups(['times:view'])]
    public function getDeparturesIn(): ?string
    {
        return Carbon::instance($this->departure_at)->diffForHumans();
    }

    public function setDepartureAt(\DateTimeImmutable $departure_at): self
    {
        $this->departure_at = $departure_at;

        return $this;
    }
}
