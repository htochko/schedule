<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: RouteRepository::class)]
class Route
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'routes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Line $line = null;

    #[ORM\OneToMany(mappedBy: 'stop', targetEntity: Departure::class, orphanRemoval: true)]
    private Collection $departures;


    #[ORM\Column(length: 60)]
    private ?string $systemName = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'routes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stop $stop = null;

    #[ORM\Column]
    private ?bool $direction = null;

    #[ORM\Column]
    private ?\DateInterval $interval = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $synced_at = null;

    public function __construct() {
        $this->departures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInterval(): ?\DateInterval
    {
        return $this->interval;
    }

    public function setInterval(\DateInterval $interval): self
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * @return Collection<int, Departure>
     */
    public function getDepartures(): Collection
    {
        return $this->departures;
    }

    public function getSyncedAt(): ?\DateTimeImmutable
    {
        return $this->synced_at;
    }

    public function setSyncedAt(?\DateTimeImmutable $synced_at): self
    {
        $this->synced_at = $synced_at;

        return $this;
    }
}
