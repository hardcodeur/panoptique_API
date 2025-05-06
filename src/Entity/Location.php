<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: LocationNote::class)]
    private Collection $locationNotes;

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

    public function addLocationNote(LocationNote $locationNote): static
    {
        if (!$this->locationNotes->contains($locationNote)) {
            $this->locationNotes->add($locationNote);
            $locationNote->setLocation($this);
        }

        return $this;
    }

    public function removeLocationNote(LocationNote $locationNote): static
    {
        if ($this->locationNotes->removeElement($locationNote)) {
            // set the owning side to null (unless already changed)
            if ($locationNote->getLocation() === $this) {
                $locationNote->setLocation(null);
            }
        }

        return $this;
    }
}
