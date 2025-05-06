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

    private const ROLES=['admin', 'manager', 'team_manager', 'agent'];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {   
        
        $faker = Factory::create("fr_FR");
        $teamFixture = TeamFixture::TEAMLIST;
        $cpt=0;
        // On parcourt toutes les équipes
        foreach ($teamFixture as $teamKey) {
            $team = $this->getReference($teamKey, Team::class);
            
            // Créer 3 utilisateurs par équipe
            for ($i = 0; $i < 3; $i++) {
                $user = new User();
                $user->setFirstName($faker->firstName());
                $user->setLastName($faker->lastName());
                $user->setPhone($faker->phoneNumber());
                $user->setTeam($team);
                $user->setStatus(1);

                $authUser = new AuthUser();
                // $email = strtolower($team->getName()) . "{$i}@sgs.com";
                $email="test{$cpt}@sgs.com";
                $authUser->setEmail($email);
                $authUser->setPassword($this->passwordHasher->hashPassword($authUser, "Root_123"));
                
                // Le premier utilisateur de l'équipe est team_manager, les autres sont agent
                $authUser->setRoles($i === 0 ? self::ROLES[2] : self::ROLES[3]);
                
                $user->setAuthUser($authUser);
                
                $manager->persist($authUser);
                $manager->persist($user);
                $cpt++;
            }
        }
        
        // Créer les admins et managers (en plus des utilisateurs par équipe)
        $this->createAdminUsers($manager, $faker);
        $this->createManagerUsers($manager, $faker);
        
        $manager->flush();
    }

    private function createAdminUsers(ObjectManager $manager, \Faker\Generator $faker): void
    {
        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setPhone($faker->phoneNumber());
            $user->setStatus(1);

            $authUser = new AuthUser();
            $authUser->setEmail("admin{$i}@sgs.com");
            $authUser->setPassword($this->passwordHasher->hashPassword($authUser, "Root_123"));
            $authUser->setRoles(self::ROLES[0]);
            
            $user->setAuthUser($authUser);
            
            $manager->persist($authUser);
            $manager->persist($user);
            
        }
    }

    private function createManagerUsers(ObjectManager $manager, \Faker\Generator $faker): void
    {
        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setPhone($faker->phoneNumber());
            $user->setStatus(1);

            $authUser = new AuthUser();
            $authUser->setEmail("manager{$i}@sgs.com");
            $authUser->setPassword($this->passwordHasher->hashPassword($authUser, "Root_123"));
            $authUser->setRoles(self::ROLES[1]);
            
            $user->setAuthUser($authUser);
            
            $manager->persist($authUser);
            $manager->persist($user);
        }
    }

    public function getDependencies(): array
    {
        return [
            TeamFixture::class,
        ];
    }
}