<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata;
use App\State\Team\TeamProvider;
use App\Dto\Team\TeamListDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

//DTO 
use App\Dto\Team\TeamCreateDto;
use App\Dto\Team\TeamDetailDto;
use App\Dto\Team\TeamUpdateDto;
use App\Dto\TeamUsers\TeamUsersDto;

// STATE
use App\State\Team\TeamProcessor;
use App\State\Team\TeamItemProvider;
use App\State\TeamUsers\TeamUsersProvider;

#[ApiResource(
    operations: [
        new Metadata\GetCollection(
            uriTemplate: '/team/list/name',
            output: TeamListDto::class,
            provider: TeamProvider::class
        ),
        new Metadata\GetCollection(
            uriTemplate: '/team/list/member',
            output: TeamUsersDto::class,
            provider: TeamUsersProvider::class
        ),
        new Metadata\Get(
            uriTemplate: '/team/{id}',
            output: TeamDetailDto::class,
            provider: TeamItemProvider::class
        ),
        new Metadata\Post(
            uriTemplate: '/team',
            input: TeamCreateDto::class,
            output: TeamDetailDto::class,
            processor: TeamProcessor::class,
        ),
        new Metadata\Patch(
            uriTemplate: '/team/{id}',
            input: TeamUpdateDto::class,
            output: TeamDetailDto::class,
            processor: TeamProcessor::class,
        ),
        new Metadata\Delete(
            uriTemplate: '/team/{id}',
            processor: TeamProcessor::class,
        ),
    ]
)]
#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $is_deleted = false;

    #[ORM\OneToMany(mappedBy: "team", targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: "team", targetEntity: Location::class)]
    private Collection $locations;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->locations = new ArrayCollection();
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

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getLocation(): Collection
    {
        return $this->locations;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->is_deleted = $isDeleted;

        return $this;
    }
}
