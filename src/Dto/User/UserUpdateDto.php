<?php

namespace App\Dto\User;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Team;

/**
 * DTO for updating an existing User via API
 */
class UserUpdateDto
{
    public function __construct(

        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères'
        )]
        private ?string $firstName = null,
        
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
        )]
        private ?string $lastName = null,
        
        #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide')]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9._-]+@sgs\.(com|fr)$/',
            message: "L'email doit être une adresse sgs"
        )]
        private ?string $email = null,
        
        #[Assert\Regex(
            pattern: '/^(?:(?:\+|00)33|0)[1-9]\d{8}$/',
            message: "Le numéro de téléphone saisi est invalide. Utilisez le format 0123456789 ou +33123456789."
        )]
        private ?string $phone = null,

        
        #[Assert\Choice(
            choices: ['admin', 'manager', 'team_manager', 'agent'],
            message: 'Le rôle {{ value }} n\'est pas valide. Rôles valides: admin, manager, team_manager, agent'
        )]
        private ?string $role = null,

        private ?Team $team = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

   public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
        
    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }
}