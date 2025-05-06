<?php 
namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\LocationLocationNote\LocationLocationNoteDto;
use App\State\LocationLocationNote\LocationLocationNoteProvider;


#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/mission/locations',
            provider: LocationLocationNoteProvider::class,
            output: LocationLocationNoteDto::class,
            description: 'Récupère toutes les lieux et leurs notes.'
        )
    ]
)]
class LocationLocationNote 
{}