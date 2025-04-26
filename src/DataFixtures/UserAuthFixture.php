<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\User;
use App\Entity\AuthUser;
use Faker\Factory;
use App\Entity\Team;

class UserAuthFixture extends Fixture implements DependentFixtureInterface
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
        $teamFixture = TeamFixture::TEAMLIST;

        for($i=0; $i < 5; $i++){
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $team = $this->getReference($teamFixture[array_rand($teamFixture)],Team::class);
            $user->setPhone($faker->phoneNumber());
            $user->setTeam($team);
            $user->setStatus(1);

            $authUser = new AuthUser();
            $email="test{$i}@sgs.com";
            $authUser->setEmail($email);
            $authUser->setPassword($this->passwordHasher->hashPassword($authUser,"Root_123"));
            $authUser->setRoles($roles[array_rand($roles)]);
            $user->setAuthUser($authUser);
            
            $manager->persist($authUser);

            $manager->persist($user);
        }
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TeamFixture::class,
        ];
    }
}
