<?php

namespace App\Dto\Notification;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO pour la création d'une nouvelle Notification via l'API.
 */
class NotificationCreateDto
{
    #[Assert\NotBlank(message: "Le message de la notification ne peut pas être vide.")]
    private ?string $text = null;

    #[Assert\NotBlank(message: "L'identifiant de l'utilisateur est requis.")]
    #[Assert\Positive(message: "L'identifiant de l'utilisateur doit être un nombre positif.")]
    private ?string $userId = null;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }
}
