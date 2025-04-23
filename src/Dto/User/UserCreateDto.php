<?php

namespace App\Dto\User;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for creating a new User via API
 */
class UserCreateDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères'
        )]
        private ?string $firstName = null,
        
        #[Assert\NotBlank(message: 'Le nom est obligatoire')]
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
        )]
        private ?string $lastName = null,
        
        #[Assert\NotBlank(message: 'L\'email est obligatoire')]
        #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide')]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9._-]+@sgs\.(com|fr)$/',
            message: "L'email doit être une adresse sgs"
        )]
        private ?string $email = null,
        
        #[Assert\NotBlank(message: 'Le mot de passe est obligatoire')]
        #[Assert\Length(
            min: 8,
            minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères'
        )]
        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).*$/',
            message: 'Le mot de passe doit contenir au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial(!@#$%^&*)'
        )]
        private ?string $password = null,
        
        #[Assert\NotBlank(message: 'Le rôle est obligatoire')]
        #[Assert\Choice(
            choices: ['admin', 'manager', 'team_manager', 'agent'],
            message: 'Le rôle {{ value }} n\'est pas valide. Rôles valides: admin, manager, team_manager, agent'
        )]
        private ?string $role = null
    ) {
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
    
    public function getPassword(): ?string
    {
        return $this->password;
    }
    
    public function getRole(): ?string
    {
        return $this->role;
    }
}