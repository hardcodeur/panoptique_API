<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Team;

class TeamFixture extends Fixture
{   
    public const TEAMLIST = ["bordeaux","nante","brest"];

    public function load(ObjectManager $manager): void
    {   
        foreach(self::TEAMLIST as $name ){
            $team = new Team();
            $team->setName($name);
            $manager->persist($team);
            
            $this->addReference($name, $team);
        }

        $manager->flush();
    }
}
