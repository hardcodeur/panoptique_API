<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\LocationNote;
use App\Entity\Location;

class LocationNoteFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {   

        $notes=[
            ["titre" => "Code du portail dépôt", "note" => "196523", "location" => "blaye"],
            ["titre" => "Fermeture du cabanon", "note" => "Vider les poubelles, éteindre la clim, fermer le cabanon à clé", "location" => "blaye"],
            ["titre" => "Documents", "note" => "Récupérer les documents à l'usine", "location" => "pauillac"],
            ["titre" => "Badge d'accès", "note" => "Récupérer son badge à l'arrivée - sécurité", "location" => "montoir"],
            ["titre" => "Sécurité", "note" => "Port du gilet de sauvetage obligatoire sur les quais", "location" => "pauillac"],
            ["titre" => "Documents / talkie-walkie / clés", "note" => "Déposer au bureau si personne aux sanitaires chauffeurs PL", "location" => "brest"]
        ];

        foreach($notes as $note){
            $locationNote = new LocationNote();
            $locationNote->setTitle($note['titre']);
            $locationNote->setNote($note['note']);
            $location = $this->getReference($note['location'],Location::class);
            $locationNote->setLocation($location);

            $manager->persist($locationNote);
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
