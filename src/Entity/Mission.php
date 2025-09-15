<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata;

use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
// DTO
use App\Dto\Mission\MissionListDto;
use App\Dto\Mission\MissionDetailDto;
use App\Dto\Mission\MissionCreateDto;
use App\Dto\Mission\MissionUpdateDto;
// State
use App\State\Mission\MissionListProvider;
use App\State\Mission\MissionItemProvider;
use App\State\Mission\MissionProcessor;


#[ApiResource(
    operations: [
        new Metadata\GetCollection(
            output: MissionListDto::class,
            provider: MissionListProvider::class
        ),
        new Metadata\Get(
            uriTemplate: '/mission/{id}',
            output: MissionDetailDto::class,
            provider: MissionItemProvider::class
        ),
        new Metadata\Post(
            uriTemplate: '/mission',
            input: MissionCreateDto::class,
            output: MissionDetailDto::class,
            processor: MissionProcessor::class,
        ),
        new Metadata\Patch(
            uriTemplate: '/mission/{id}',
            input: MissionUpdateDto::class,
            output: MissionDetailDto::class,
            processor: MissionProcessor::class,
        ),
        new Metadata\Delete(
            uriTemplate: '/mission/{id}',
            processor: MissionProcessor::class,
        )
    ]
)]
#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $start = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $end = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    /**
     * @var Collection<int, Shift>
     */
    #[ORM\OneToMany(targetEntity: Shift::class, mappedBy: 'mission', orphanRemoval: true)]
    private Collection $shifts;

    public function __construct()
    {
        $this->shifts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(\DateTimeImmutable $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(\DateTimeImmutable $end): static
    {
        $this->end = $end;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection<int, Shift>
     */
    public function getShifts(): Collection
    {
        return $this->shifts;
    }

    public function addShift(Shift $shift): static
    {
        if (!$this->shifts->contains($shift)) {
            $this->shifts->add($shift);
            $shift->setMission($this);
        }

        return $this;
    }


    public function removeShift(Shift $shift): static
    {
        if ($this->shifts->removeElement($shift)) {
            if ($shift->getMission() === $this) {
                $shift->setMission(null);
            }
        }

        return $this;
    }

}
