<?php

namespace App\Entity;

use App\Repository\RouteRepository;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\ManyToOne(inversedBy: 'routes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stop $stop = null;

    #[ORM\Column]
    private ?bool $direction = null;

    #[ORM\Column]
    private ?\DateInterval $interval = null;

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
}
