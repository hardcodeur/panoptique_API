<?php 
namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\TeamUsers\TeamUsersDto;
use App\State\TeamUsers\TeamUsersProvider;
use App\Dto\TeamUsers\TeamUnassignedUsersDto;
use App\State\TeamUsers\TeamUnassignedUsersProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/team/users',
            provider: TeamUsersProvider::class,
            output: TeamUsersDto::class,
            description: 'Récupère toutes les équipes et leurs membres.'
        )
    ]
)]
class TeamUsers 
{}

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/team/unassigned-users',
            provider: TeamUnassignedUsersProvider::class,
            output: TeamUnassignedUsersDto::class,
            description: 'Récupérer tous les utilisateurs qui ne sont actuellement affectés à aucune équipe'
        )
    ]
)]
class TeamUnassignedUsers 
{}