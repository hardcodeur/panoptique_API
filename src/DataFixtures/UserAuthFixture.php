<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\AuthUser;
use Faker\Factory;

class UserAuthFixture extends Fixture
{   

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {   
        $faker= Factory::create("fr_FR");
        $roles=['admin', 'manager', 'team_manager', 'agent'];

        for($i=0; $i < 5; $i++){
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());

            $authUser = new AuthUser();
            $email="test{$i}@pto.com";
            $authUser->setEmail($email);
            $authUser->setPassword($this->passwordHasher->hashPassword($authUser,"Root_123"));
            $authUser->setRoles($roles[array_rand($roles)]);
            
            $user->setAuthUser($authUser);
            $manager->persist($authUser);

            $manager->persist($user);
        }
        
        $manager->flush();
    }
}
