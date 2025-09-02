<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata;

use App\Dto\Customer\CustomerListDto;
use App\State\Customer\CustomerListProvider;

#[ApiResource(
    operations: [
        new Metadata\GetCollection(
            uriTemplate: '/customer/list/name',
            output: CustomerListDto::class,
            provider: CustomerListProvider::class
        ),
        // new Metadata\Get(
        //     output: UserDetailDto::class,
        //     provider: UserItemProvider::class
        // ),
        // new Metadata\Post(
        //     input: UserCreateDto::class,
        //     output: UserDetailDto::class,
        //     processor: UserProcessor::class,
        // ),
        // new Metadata\Put(
        //     input: UserUpdateDto::class,
        //     output: UserDetailDto::class,
        //     processor: UserProcessor::class,
        // ),
        new Metadata\Delete()
    ]
)]
#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $product = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $is_deleted = false;

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

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?location $location): static
    {
        $this->location = $location;

        return $this;
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
