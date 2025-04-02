<?php

namespace App\Dto\User;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

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
        private ?string $email = null,
        
        #[Assert\Length(
            min: 8,
            minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères'
        )]
        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            message: 'Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre'
        )]
        private ?string $password = null,
        
        #[Assert\Choice(
            choices: ['admin', 'manager', 'team_manager', 'agent'],
            message: 'Le rôle {{ value }} n\'est pas valide. Rôles valides: admin, manager, team_manager, agent'
        )]
        private ?string $role = null
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
    
    public function getPassword(): ?string
    {
        return $this->password;
    }
    
    public function getRole(): ?string
    {
        return $this->role;
    }
}