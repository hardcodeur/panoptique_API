<?php
namespace App\Dto\Team;

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
        /** @var UserTeamDto[] */
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
    * @return UserTeamDto[]
    */
    public function getUsers(): array
    {
        return $this->users;
    }
    
}  