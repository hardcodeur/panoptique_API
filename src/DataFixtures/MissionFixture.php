<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Mission;
use App\Entity\Team;
use App\Entity\Customer;

class MissionFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {   
        $team = TeamFixture::TEAMLIST;
        $date=new \DateTimeImmutable();
        $missions = [
            [
                "start"=>$date->setTime(23,0),
                "end"=>$date->add(new \DateInterval('P1D'))->setTime(10,0),
                "customer"=>"ccmp_bitume","team"=>$team[0]
            ],
            [
                "start"=>$date->add(new \DateInterval('P2D'))->setTime(11,30),
                "end"=>$date->add(new \DateInterval('P3D'))->setTime(23,0),
                "customer"=>"dpr_essence","team"=>$team[0]
            ],
            [
                "start"=>$date->add(new \DateInterval('P7D'))->setTime(11,30),
                "end"=>$date->add(new \DateInterval('P8D'))->setTime(23,0),
                "customer"=>"dpr_essence","team"=>$team[0]
            ],

            [
                "start"=>$date->setTime(23,0),
                "end"=>$date->add(new \DateInterval('P1D'))->setTime(10,0),
                "customer"=>"ocealab_méthanol","team"=>$team[1]
            ],
            
            [
                "start"=>$date->add(new \DateInterval('P2D'))->setTime(11,30),
                "end"=>$date->add(new \DateInterval('P3D'))->setTime(23,0),
                "customer"=>"nordGulf_phénol","team"=>$team[1]
            ],

            [
                "start"=>$date->add(new \DateInterval('P7D'))->setTime(11,30),
                "end"=>$date->add(new \DateInterval('P8D'))->setTime(23,0),
                "customer"=>"dpr_essence","team"=>$team[1]
            ],

            [
                "start"=>$date->setTime(23,0),
                "end"=>$date->add(new \DateInterval('P1D'))->setTime(10,0),
                "customer"=>"nautichem_acide_nitrique","team"=>$team[2]
            ],
            [
                "start"=>$date->add(new \DateInterval('P2D'))->setTime(11,30),
                "end"=>$date->add(new \DateInterval('P3D'))->setTime(23,0),
                "customer"=>"transGaz_gaz_liquéfié","team"=>$team[2]
            ],
            [
                "start"=>$date->add(new \DateInterval('P7D'))->setTime(11,30),
                "end"=>$date->add(new \DateInterval('P8D'))->setTime(23,0),
                "customer"=>"dpr_essence","team"=>$team[2]
            ],
        ];

        foreach($missions as $item){
            $mission = new Mission();
            $mission->setStart($item['start']);
            $mission->setEnd($item['end']);
            $customer = $this->getReference($item['customer'],Customer::class);
            $mission->setCustomer($customer);
            $team = $this->getReference($item['team'],Team::class);
            $mission->setTeam($team);
            $manager->persist($mission);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CustomerFixture::class,
            TeamFixture::class
        ];
    }
}
