<?php 
namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\MissionShifts\MissionShiftsDto;
use App\State\MissionShifts\MissionShiftsProvider;


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