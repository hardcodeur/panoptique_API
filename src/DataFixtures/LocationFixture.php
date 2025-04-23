<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Location;
use App\Entity\Team;


class LocationFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $location_list=[
            ["name"=>"bassens","address"=>"Quai Alfred de Vial, 33530 Bassens","team"=>"bordeaux"],
            ["name"=>"pauillac","address"=>"Trompeloup, 33250 Pauillac","team"=>"bordeaux"],
            ["name"=>"blaye","address"=>"26 Cr Bacalan, 33390 Blaye","team"=>"bordeaux"],
            ["name"=>"montoir","address"=>"Rue de la Pierre Percée, BP 9, 44550 Montoir-de-Bretagne","team"=>"nante"],
            ["name"=>"cheviré","address"=>"Quai de Cheviré, 44100 Nantes","team"=>"nante"],
            ["name"=>"saint-Marc","address"=>"465 rue Alain Colas, ZI Portuaire de Saint-Marc, 29200 Brest","team"=>"brest"],
            ["name"=>"brest","address"=>"275 rue Monjaret de Kerjégu, 29200 Brest","team"=>"brest"],
        ];

        foreach($location_list as $item){
            $location = new Location();
            $location->setName($item['name']);
            $location->setAddress($item["address"]);
            $team = $this->getReference($item['team'],Team::class);
            $location->setTeam($team);
            $manager->persist($location);
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


