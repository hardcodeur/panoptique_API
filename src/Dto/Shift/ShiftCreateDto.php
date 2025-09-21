<?php

namespace App\Dto\Shift;

use Symfony\Component\Validator\Constraints as Assert;

class ShiftCreateDto
{
    public function __construct(
        
        #[Assert\NotBlank(message: "La date de début de quart est obligatoire")]
        #[Assert\Type(type: "\DateTimeImmutable", message: "Le format de la date est invalide")]
        private ?\DateTimeImmutable $start = null,
        
        #[Assert\NotBlank(message: "La date de fin de quart est obligatoire")]
        #[Assert\Type(type: "\DateTimeImmutable", message: "Le format de la date est invalide")]
        private ?\DateTimeImmutable $end = null,

        #[Assert\NotBlank(message: 'Le type de quart est obligatoire')]
        #[Assert\Choice(
            choices: ['connexion', 'surveillance', 'deconnexion'],
            message: 'Le type de quart  {{ value }} n\'est pas valide. Type de quart  valides: connexion, surveillance, deconnexion'
        )]
        private ?string $activity = null,

        #[Assert\NotBlank(message: "Les agents affecté aux quarts sont obligatoire")]
        #[Assert\Type(type: 'array')]
        private ?array $users = null,
        
    ) {
    }

    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(?\DateTimeImmutable $start): self
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeImmutable $end): self
    {
        $this->end = $end;
        return $this;
    }

    public function getActivity(): ?string
    {
        return $this->activity;
    }

    public function setActivity(?string $activity): self
    {
        $this->activity = $activity;
        return $this;
    }

    public function getUsers(): ?array
    {
        return $this->users;
    }

    public function setUsers(?array $users): self
    {
        $this->users = $users;
        return $this;
    }

}
