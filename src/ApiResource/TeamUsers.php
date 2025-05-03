<?php 
namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\Team\TeamUsersDto;
use App\State\Team\TeamUsersProvider;

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