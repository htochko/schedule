<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: LineRepository::class)]
class Line
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'stop', targetEntity: Departure::class, orphanRemoval: true)]
    private Collection $departures;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $synced_at = null;

    #[ORM\OneToMany(mappedBy: 'line', targetEntity: Route::class, orphanRemoval: true)]
    private Collection $routes;

    public function __construct()
    {
        $this->departures = new ArrayCollection();
        $this->routes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSyncedAt(): ?\DateTimeImmutable
    {
        return $this->synced_at;
    }

    /**
     * @return Collection<int, Departure>
     */
    public function getDepartures(): Collection
    {
        return $this->departures;
    }

    public function setSyncedAt(?\DateTimeImmutable $synced_at): self
    {
        $this->synced_at = $synced_at;

        return $this;
    }

    /**
     * todo addDeparture removeDeparture
     */

    /**
     * @return Collection<int, Route>
     */
    public function getRoutes(): Collection
    {
        return $this->routes;
    }

    public function addRoute(Route $route): self
    {
        if (!$this->routes->contains($route)) {
            $this->routes[] = $route;
            $route->setLine($this);
        }

        return $this;
    }

    public function removeRoute(Route $route): self
    {
        if ($this->routes->removeElement($route)) {
            // set the owning side to null (unless already changed)
            if ($route->getLine() === $this) {
                $route->setLine(null);
            }
        }

        return $this;
    }
}
