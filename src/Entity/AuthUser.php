<?php

namespace App\Entity;

use App\Repository\AuthUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AuthUserRepository::class)]
class AuthUser 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'authUser', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLogin = null;

    private const ROLE = [
        "admin"=>"ROLE_ADMIN",
        "manager"=>"ROLE_MANAGER",
        "team_manager"=>"ROLE_TEAM_MANAGER",
        "agent"=>"ROLE_USER"
    ];

    private const ROLE_HIERARCHY = [
        'admin' => [self::ROLE["admin"], self::ROLE["manager"], self::ROLE["team_manager"], self::ROLE["agent"]],
        'manager' => [self::ROLE["manager"], self::ROLE["team_manager"], self::ROLE["agent"]],
        'team_manager' => [self::ROLE["team_manager"], self::ROLE["agent"]],
        'agent' => [self::ROLE["agent"]]
    ];


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(string $role): static
    {   
        if(!array_key_exists($role,self::ROLE_HIERARCHY) ){
            throw new \InvalidArgumentException(sprintf('Rôle "%s" invalide. Rôles valides: %s',$role,implode(', ', array_keys(self::ROLE_HIERARCHY))));
        }
        $this->roles = self::ROLE_HIERARCHY[$role];
        return $this;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeImmutable $lastLogin): static
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }
}
