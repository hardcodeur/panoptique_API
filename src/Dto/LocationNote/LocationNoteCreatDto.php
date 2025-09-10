<?php

namespace App\Dto\LocationNote;

use Symfony\Component\Validator\Constraints as Assert;

class LocationNoteCreatDto
{   

    public function __construct(
        #[Assert\NotBlank(message: "Le titre ne peut pas être vide.")]
        #[Assert\Type("string")]
        #[Assert\Length(min: 2, max: 100, minMessage: "Le titre doit contenir au moins {{ limit }} caractères.", maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères.")]
        private ?string $title = null,
        #[Assert\Type("string")]
        #[Assert\NotBlank(message: "La note ne peut pas être vide.")]
        private ?string $note = null,
    ) {
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }
}