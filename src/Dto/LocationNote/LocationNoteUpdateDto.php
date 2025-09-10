<?php

namespace App\Dto\LocationNote;

use Symfony\Component\Validator\Constraints as Assert;

class LocationNoteUpdateDto
{
    public function __construct(

        private ?int $id = null,
        #[Assert\Type("string")]
        #[Assert\Length(min: 2, max: 255, minMessage: "Le titre doit contenir au moins {{ limit }} caractères.", maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères.")]
        private ?string $title = null,

        #[Assert\Type("string")]
        private ?string $note = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }
}
