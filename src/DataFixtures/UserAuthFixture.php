<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\AuthUser;
use Faker\Factory;

class UserAuthFixture extends Fixture
{   

    public function load(ObjectManager $manager): void
    {   
        $faker= Factory::create("fr_FR");
        $roles=['admin', 'manager', 'team_manager', 'agent'];

        for($i=0; $i < 5; $i++){
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());

            $authUser = new AuthUser();
            $authUser->setEmail($faker->email());
            $authUser->setPassword("root");
            $authUser->setRoles($roles[array_rand($roles)]);
            
            $user->setAuthUser($authUser);

            $manager->persist($user);
            $manager->persist($authUser);
        }
        
        $manager->flush();
    }
}
