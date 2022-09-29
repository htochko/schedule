<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\StopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['stop:routes']]
)]
#[ORM\Entity(repositoryClass: StopRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
class Stop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[Groups('stop:routes')]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $systemName = null;

    #[ORM\Column(length: 16)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'stop', targetEntity: Route::class, orphanRemoval: true)]
    private Collection $routes;

    #[ORM\Column(nullable: true)]
    private ?float $lon = null;

    #[ORM\Column(nullable: true)]
    private ?float $lat = null;

    #[ApiSubresource]
    #[Groups('stop:routes')]
    #[ORM\OneToMany(mappedBy: 'stop', targetEntity: StopTime::class, orphanRemoval: true)]
    private Collection $stopTimes;

    public function __construct()
    {
        $this->routes = new ArrayCollection();
        $this->stopTimes = new ArrayCollection();
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

    public function getSystemName(): ?int
    {
        return $this->systemName;
    }

    public function setSystemName(int $systemName): self
    {
        $this->systemName = $systemName;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

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
            $route->setStop($this);
        }

        return $this;
    }

    public function removeRoute(Route $route): self
    {
        if ($this->routes->removeElement($route)) {
            // set the owning side to null (unless already changed)
            if ($route->getStop() === $this) {
                $route->setStop(null);
            }
        }

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }


    public function getStopTimes(): Collection
    {
        return $this->stopTimes;
    }

    public function addStopTime(StopTime $stopTime): self
    {
        if (!$this->stopTimes->contains($stopTime)) {
            $this->stopTimes[] = $stopTime;
            $stopTime->setStop($this);
        }

        return $this;
    }

    public function removeStopTime(StopTime $stopTime): self
    {
        if ($this->stopTimes->removeElement($stopTime)) {
            // set the owning side to null (unless already changed)
            if ($stopTime->getStop() === $this) {
                $stopTime->setStop(null);
            }
        }

        return $this;
    }
}
