<?php 
namespace App\ApiResource\user;

use ApiPlatform\Metadata;
use ApiPlatform\Metadata\ApiResource;

use App\Dto\User\UserListDto;
use App\State\User\UserTeamProvider;



#[ApiResource(
    operations: [
        new Metadata\GetCollection(
            uriTemplate: '/users/team',
            output: UserListDto::class,
            provider: UserTeamProvider::class,
            name:"users_team"
        ),
    ]
)]
class user{

}