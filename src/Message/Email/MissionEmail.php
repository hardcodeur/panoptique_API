<?php

namespace App\Message\Email;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Mission;

class MissionEmail
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    public function registreNewMission(string $userEmail, Mission $mission)
    {
        $missionId = $mission->getId();
        $dateStart = $mission->getStart()->format('d/m/Y H:i');
        $dateEnd = $mission->getEnd()->format('d/m/Y H:i');
        $customer = $mission->getCustomer()->getName();
        $team = $mission->getTeam()->getName();

        $email = (new Email())
            ->from("no-reply@panoptique.com")
            ->to($userEmail)
            ->subject("Nouvelle mission - Panoptique")
            ->text("Bonjour,\n\nUne nouvelle mission vous a été assignée.\n\nLes quarts ne sont pas encore définis.\n\n- Mission " . $missionId . "\n- Début : " . $dateStart . "\n- Fin : " . $dateEnd . "\n- Client : " . $customer . "\n- Équipe : " . $team . "\n\nPanoptique")
            ->html(
            "<p>Bonjour,</p>
            <p>Une nouvelle mission vous a été assignée.</p>
            <p>Les quarts ne sont pas encore définis</p>
            <ul>
                <li><strong>Mission " . $missionId . "</strong></li>
                <li><strong>Début :</strong> " . $dateStart . "</li>
                <li><strong>Fin :</strong> " . $dateEnd . "</li>
                <li><strong>Client :</strong> " . $customer . "</li>
                <li><strong>Équipe :</strong> " . $team . "</li>
            </ul>
            <p>Panoptique</p>"
        );

        $this->mailer->send($email);
    }

    public function registreDeleteMission(string $userEmail, Mission $mission)
    {
        $missionId = $mission->getId();
        $dateStart = $mission->getStart()->format('d/m/Y H:i');
        $dateEnd = $mission->getEnd()->format('d/m/Y H:i');
        $customer = $mission->getCustomer()->getName();

        $email = (new Email())
            ->from("no-reply@panoptique.com")
            ->to($userEmail)
            ->subject("Annulation de mission - Panoptique")
            ->text("Bonjour,\n\nLa mission " . $missionId . " a été annulée.\n\n- Début : " . $dateStart . "\n- Fin : " . $dateEnd . "\n\nPanoptique")
            ->html(
            "<p>Bonjour,</p>
            <p>La mission  a été annulée.</p>
            <ul>
                <li><strong>Mission " . $missionId . "</strong></li>
                <li><strong>Début :</strong> " . $dateStart . "</li>
                <li><strong>Fin :</strong> " . $dateEnd . "</li>
                <li><strong>Client :</strong> " . $customer . "</li>
            </ul>
            <p>Panoptique</p>"
        );

        $this->mailer->send($email);
    }
}