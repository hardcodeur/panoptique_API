<?php 
namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\Profil\ProfilDetailDto;
use App\State\Profil\ProfilDetailProvider;


#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/profil/{id}',
            requirements: ['id' => '\d+'],
            provider: ProfilDetailProvider::class,
            output: ProfilDetailDto::class,
            description: "Récupère le profil détaillé d'un utilisateur membre de l'équipe"
        )
    ]
)]
class Profil
{}