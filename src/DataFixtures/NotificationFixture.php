<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

use App\Entity\User;
use App\Entity\Notification;


class NotificationFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {   
        $faker = Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();

        foreach($users as $user){
            // 3 notif by user
            for ($i=0; $i < 3; $i++) { 
                $notification = new Notification();
                $notification->setUserId($user); // On passe l'objet User entier
                $notification->setText($faker->sentence(10, true));
                $notification->setValueCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('first day of this month', 'now')));  // Date passée dans le mois actuel  
                $manager->persist($notification);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserAuthFixture::class
        ];
    }
}
