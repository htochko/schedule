<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiSubresource]
#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[Groups('stop:routes')]
    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Line $line = null;

    #[Groups('stop:routes')]
    #[ORM\Column]
    private ?int $day = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $systemName = null;

    #[ORM\Column(length: 255)]
    private ?string $header = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $synced_at = null;

    #[ORM\OneToMany(mappedBy: 'trip', targetEntity: StopTime::class, orphanRemoval: true)]
    private Collection $stopTimes;

    #[ORM\Column(nullable: true)]
    private ?bool $direction = null;

    public function __construct()
    {
        $this->stopTimes = new ArrayCollection();
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

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getSystemName(): ?string
    {
        return $this->systemName;
    }

    public function setSystemName(string $systemName): self
    {
        $this->systemName = $systemName;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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

    /**
     * @return Collection<int, StopTime>
     */
    public function getStopTimes(): Collection
    {
        return $this->stopTimes;
    }

    public function addStopTime(StopTime $stopTime): self
    {
        if (!$this->stopTimes->contains($stopTime)) {
            $this->stopTimes[] = $stopTime;
            $stopTime->setTrip($this);
        }

        return $this;
    }

    public function removeStopTime(StopTime $stopTime): self
    {
        if ($this->stopTimes->removeElement($stopTime)) {
            // set the owning side to null (unless already changed)
            if ($stopTime->getTrip() === $this) {
                $stopTime->setTrip(null);
            }
        }

        return $this;
    }

    public function isDirection(): ?bool
    {
        return $this->direction;
    }

    public function setDirection(?bool $direction): self
    {
        $this->direction = $direction;

        return $this;
    }
}
