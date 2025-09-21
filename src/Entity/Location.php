<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata;
use Doctrine\ORM\Mapping as ORM;

use App\Dto\Location\LocationListDto;
use App\State\Location\LocationProvider;
use App\State\Location\LocationTeamListProvider; 
use App\Dto\Location\LocationCreateDto;
use App\State\Location\LocationProcessor;
use App\Dto\Location\LocationDetailDto;
use App\Dto\Location\LocationUpdateDto;


#[ApiResource(
    operations: [
        new Metadata\GetCollection(
            output: LocationListDto::class,
            provider: LocationProvider::class
        ),
        new Metadata\Post(
            uriTemplate: '/location',
            input: LocationCreateDto::class,
            output: LocationDetailDto::class,
            processor: LocationProcessor::class,
        ),
        new Metadata\Patch(
            uriTemplate: '/location/{id}',
            input: LocationUpdateDto::class,
            output: LocationDetailDto::class,
            processor: LocationProcessor::class,
        )
    ]
)]
#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{   
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 200)]
    private ?string $address = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Team $team = null;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: LocationNote::class, cascade: ["remove"], orphanRemoval: true)]
    private Collection $locationNotes;

    public function __construct(){
        $this->locationNotes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getTeam(): ?team
    {
        return $this->team;
    }

    public function setTeam(?team $team): static
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection<int, LocationNote>
     */
    public function getLocationNotes(): Collection
    {
        return $this->locationNotes;
    }
}
