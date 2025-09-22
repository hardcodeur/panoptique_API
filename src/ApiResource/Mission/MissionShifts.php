<?php 
namespace App\ApiResource\Mission;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\Mission\MissionListDto;
use App\State\Mission\MissionTeamListProvider;


#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/missions/team',
            provider: MissionTeamListProvider::class,
            output: MissionListDto::class,
            description: 'Récupère toutes les missions et leurs quarts.'
        )
    ]
)]
class MissionShifts 
{}