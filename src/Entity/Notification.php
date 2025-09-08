<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata;
use Doctrine\ORM\Mapping as ORM;

use App\Dto\Notification\NotificationListDto;
use App\Dto\Notification\NotificationCreateDto;
use App\Dto\Notification\NotificationDetailDto;

use App\State\Notification\NotificationListProvider;
use App\State\Notification\NotificationProcessor;

#[ApiResource(
    operations: [
        new Metadata\Get(
            uriTemplate: '/notifications/user/{id}/',
            output: NotificationListDto::class,
            provider: NotificationListProvider::class,
            name: 'get_user_notifications'
        ),
        new Metadata\Post(
            uriTemplate: '/notification',
            input: NotificationCreateDto::class,
            output: NotificationDetailDto::class,
            processor: NotificationProcessor::class,
        ),
        new Metadata\Delete(
            uriTemplate: '/notification/{id}',
            processor: NotificationProcessor::class,
        )
    ]
)]
#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $is_delete = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }
    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->created_at = new \DateTimeImmutable();

        return $this;
    }

    public function setValueCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isDelete(): ?bool
    {
        return $this->is_delete;
    }

    public function setIsDelete(bool $is_delete): static
    {
        $this->is_delete = $is_delete;

        return $this;
    }
}
