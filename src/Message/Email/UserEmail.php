<?php

namespace App\Message\Email;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserEmail{

    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function registreNewUser(string $userEmail, string $generatedPassword){
        $email = (new Email())
        ->from("no-reply@panoptique.com")
        ->to($userEmail)
        ->subject('Bienvenue sur Panoptique !')
        ->text('Votre compte a été créé. Votre mot de passe est : ' . $generatedPassword)
        ->html('<p>Bonjour et bienvenue !</p><p>Votre compte a été créé avec succès.</br>
        </p><p>Voici votre mot de passe temporaire :<strong>' . $generatedPassword . '</strong></p>');

        $this->mailer->send($email);
    }
}
