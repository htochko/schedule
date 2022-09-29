<?php

namespace App\Entity;

use App\Repository\StopTimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StopTimeRepository::class)]
class StopTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stopTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trip $trip = null;

    #[ORM\ManyToOne(inversedBy: 'stopTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stop $stop = null;

    #[ORM\Column(nullable: true)]
    private ?int $sequence = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $synced_at = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $departure_at = null;

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

    public function setDepartureAt(\DateTimeImmutable $departure_at): self
    {
        $this->departure_at = $departure_at;

        return $this;
    }
}
