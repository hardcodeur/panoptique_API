<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata;
use App\State\User\UserProvider;
use App\State\User\UserItemProvider;
use App\State\User\UserProcessor;
use App\Dto\User\UserListDto;
use App\Dto\User\UserDetailDto;
use App\Dto\User\UserCreateDto;
use App\Dto\User\UserUpdateDto;
use App\Dto\User\UserDetailUpdateDto;

use App\Dto\UserShifts\UserShiftsOutputDto;
use App\State\UserShifts\UserShiftsProvider;

use App\Dto\UserShifts\UseShiftMetricOutputDto;
use App\State\UserShifts\UseShiftMetricProvider;

#[ApiResource(
    operations: [
        new Metadata\GetCollection(
            output: UserListDto::class,
            provider: UserProvider::class
        ),
        new Metadata\Get(
            output: UserDetailDto::class,
            provider: UserItemProvider::class
        ),
        new Metadata\Post(
            input: UserCreateDto::class,
            output: UserDetailDto::class,
            processor: UserProcessor::class,
        ),
        new Metadata\Patch(
            input: UserUpdateDto::class,
            output: UserDetailUpdateDto::class,
            processor: UserProcessor::class,
        ),
        new Metadata\Delete(
            processor: UserProcessor::class,
        ),
        new Metadata\Get(
            uriTemplate: '/users/{userId}/current-week-shifts',
            uriVariables: [
                'userId' => new Metadata\Link(fromClass: User::class)
            ],
            output: UserShiftsOutputDto::class,
            provider: UserShiftsProvider::class,
            name: 'user_current_week_shifts'
        ),
        new Metadata\Get(
            uriTemplate: '/users/{userId}/metric-shift',
            uriVariables: [
                'userId' => new Metadata\Link(fromClass: User::class)
            ],
            output: UseShiftMetricOutputDto::class,
            provider: UseShiftMetricProvider::class,
            name: 'user_current_month_shifts_metric'
        )
    ]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $first_name = null;

    #[ORM\Column(length: 100)]
    private ?string $last_name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private ?AuthUser $authUser = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $status = null;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'id', nullable: true)]
    private ?Team $team = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profil_picture_path = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;
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

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getAuthUser(): ?AuthUser
    {
        return $this->authUser;
    }

    public function setAuthUser(AuthUser $authUser): static
    {
        // set the owning side of the relation if necessary
        if ($authUser->getUser() !== $this) {
            $authUser->setUser($this);
        }

        $this->authUser = $authUser;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): static
    {
        $this->status = $status;

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

    public function getProfilPicturePath(): ?string
    {
        return $this->profil_picture_path;
    }

    public function setProfilPicturePath(?string $profil_picture_path): static
    {
        $this->profil_picture_path = $profil_picture_path;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

}
