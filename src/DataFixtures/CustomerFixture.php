<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Customer;
use App\Entity\Location;

class CustomerFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {   

        $customer_liste=[
            ["name"=>"ccmp","product"=>"bitume","location"=>"blaye"],
            ["name"=>"donitient","product"=>"engrais liquide","location"=>"blaye"],
            ["name"=>"dpr","product"=>"essence","location"=>"pauillac"],
            ["name"=>"dpr","product"=>"esp 32","location"=>"pauillac"],
            ["name"=>"forsa","product"=>"bitume","location"=>"bassens"],
            ["name"=>"ocealab","product"=>"méthanol","location"=>"saint-marc"],
            ["name"=>"nordGulf","product"=>"phénol","location"=>"brest"],
            ["name"=>"brestochem","product"=>"toluène","location"=>"brest"],
            ["name"=>"sealogis","product"=>"chlore liquide","location"=>"cheviré"],
            ["name"=>"nautichem","product"=>"acide nitrique","location"=>"cheviré"],
            ["name"=>"nautichem","product"=>"acide sulfurique","location"=>"montoir"],
            ["name"=>"transGaz","product"=>"gaz liquéfié","location"=>"montoir"],
        ];

        foreach($customer_liste as $item){
            $customer = new Customer();
            $customer->setName($item["name"]);
            $customer->setProduct($item["product"]);
            $location = $this->getReference($item['location'],Location::class);
            $customer->setLocation($location);
            $manager->persist($customer);

            $referenceKeyUnique = sprintf('%s_%s', $item['name'], str_replace(' ', '_', strtolower($item['product'])));
            $this->addReference($referenceKeyUnique,$customer);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LocationFixture::class,
        ];
    }
}
