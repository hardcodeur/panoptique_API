<?php 
namespace App\ApiResource\Mission;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\Mission\MissionShiftsDto;
use App\State\Mission\MissionShiftsProvider;


#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/missions/shifts',
            provider: MissionShiftsProvider::class,
            output: MissionShiftsDto::class,
            description: 'Récupère toutes les missions et leurs quarts.'
        )
    ]
)]
class MissionShifts 
{}