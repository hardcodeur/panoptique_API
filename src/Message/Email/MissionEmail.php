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

        $dateStart = $mission->getStart()->format('d/m/Y H:i');
        $dateEnd = $mission->getEnd()->format('d/m/Y H:i');
        $customer = $mission->getCustomer()->getName();
        $team = $mission->getTeam()->getName();

        $email = (new Email())
            ->from("no-reply@panoptique.com")
            ->to($userEmail)
            ->subject("Panoptique nouvelle mission")
            ->text("Bonjour,\n\nUne nouvelle mission vous a été assignée.\n\nLes quarts ne sont pas encore définis.\n\n- Début : " . $dateStart . "\n- Fin : " . $dateEnd . "\n- Client : " . $customer . "\n- Équipe : " . $team . "\n\nL'équipe Panoptique")
            ->html("
            <p>Bonjour,</p>
            <p>Une nouvelle mission vous a été assignée.</p>
            <p>Les quarts ne sont pas encore définis</p>
            <ul>
                <li><strong>Début :</strong> " . $dateStart . "</li>
                <li><strong>Fin :</strong> " . $dateEnd . "</li>
                <li><strong>Client :</strong> " . $customer . "</li>
                <li><strong>Équipe :</strong> " . $team . "</li>
            </ul>
            <p>L'équipe Panoptique</p>"
        );

        $this->mailer->send($email);
    }
}
