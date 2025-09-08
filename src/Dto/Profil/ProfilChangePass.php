<?php

namespace App\Dto\Profil;

use Symfony\Component\Validator\Constraints as Assert;

class ProfilChangePass
{
    #[Assert\NotBlank(message: "Le nouveau mot de passe est requis.")]
    #[Assert\Length(
        min: 8,
        minMessage: "Le nouveau mot de passe doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $newPass = null;

    #[Assert\NotBlank(message: "La confirmation du mot de passe est requise.")]
    #[Assert\EqualTo(
        propertyPath: "newPass",
        message: "La confirmation et le nouveau mot de passe ne correspondent pas."
    )]
    private ?string $confirmNewPass = null;

    public function getNewPass(): ?string
    {
        return $this->newPass;
    }

    public function setNewPass(?string $newPass): self
    {
        $this->newPass = $newPass;
        return $this;
    }

    public function getConfirmNewPass(): ?string
    {
        return $this->confirmNewPass;
    }

    public function setConfirmNewPass(?string $confirmNewPass): self
    {
        $this->confirmNewPass = $confirmNewPass;
        return $this;
    }
}