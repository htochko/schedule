<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $monday = null;

    #[ORM\Column(nullable: true)]
    private ?int $tuesday = null;

    #[ORM\Column(nullable: true)]
    private ?int $wednesday = null;

    #[ORM\Column(nullable: true)]
    private ?int $thursday = null;

    #[ORM\Column(nullable: true)]
    private ?int $friday = null;

    #[ORM\Column(nullable: true)]
    private ?int $saturday = null;

    #[ORM\Column(nullable: true)]
    private ?int $sunday = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $start_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $end_at = null;

    #[ORM\Column(nullable: true)]
    private ?bool $synced = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonday(): ?int
    {
        return $this->monday;
    }

    public function setMonday(?int $monday): self
    {
        $this->monday = $monday;

        return $this;
    }

    public function getTuesday(): ?int
    {
        return $this->tuesday;
    }

    public function setTuesday(?int $tuesday): self
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    public function getWednesday(): ?int
    {
        return $this->wednesday;
    }

    public function setWednesday(?int $wednesday): self
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    public function getThursday(): ?int
    {
        return $this->thursday;
    }

    public function setThursday(?int $thursday): self
    {
        $this->thursday = $thursday;

        return $this;
    }

    public function getFriday(): ?int
    {
        return $this->friday;
    }

    public function setFriday(?int $friday): self
    {
        $this->friday = $friday;

        return $this;
    }

    public function getSaturday(): ?int
    {
        return $this->saturday;
    }

    public function setSaturday(?int $saturday): self
    {
        $this->saturday = $saturday;

        return $this;
    }

    public function getSunday(): ?int
    {
        return $this->sunday;
    }

    public function setSunday(?int $sunday): self
    {
        $this->sunday = $sunday;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTimeImmutable $start_at): self
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTimeImmutable $end_at): self
    {
        $this->end_at = $end_at;

        return $this;
    }

    public function isSynced(): ?bool
    {
        return $this->synced;
    }

    public function setSynced(?bool $synced): self
    {
        $this->synced = $synced;

        return $this;
    }
}
