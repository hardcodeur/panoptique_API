<?php
namespace App\Dto\TeamUsers;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for User list representation in API responses
 */
class TeamUsersDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?string $teamName = null,
        /** @var UserDto[] */
        private array $users = [],
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    /**
    * @return UserDto[]
    */
    public function getUsers(): array
    {
        return $this->users;
    }
    
}  