<?php 
namespace App\ApiResource\Location;

use ApiPlatform\Metadata;
use ApiPlatform\Metadata\ApiResource;

use App\Dto\Location\LocationListDto;
use App\State\Location\LocationTeamListProvider;

#[ApiResource(
    operations: [
        new Metadata\GetCollection(
            uriTemplate: '/locations/team',
            output: LocationListDto::class,
            provider: LocationTeamListProvider::class,
            name: 'get_team_locations',
        ),
    ]
)]
class LocationTeam 
{}