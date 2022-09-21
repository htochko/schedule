<?php

namespace App\Entity;

use App\Repository\DepartureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartureRepository::class)]
class Departure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $day = null;

    #[ORM\ManyToOne(inversedBy: 'departures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Line $line = null;

    #[ORM\Column]
    private ?bool $direction = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $start_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getLine(): ?Line
    {
        return $this->line;
    }

    public function setLine(?Line $line): self
    {
        $this->line = $line;

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

    public function isDirection(): ?bool
    {
        return $this->direction;
    }

    public function setDirection(bool $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTimeInterface $start_at): self
    {
        $this->start_at = $start_at;

        return $this;
    }
}
