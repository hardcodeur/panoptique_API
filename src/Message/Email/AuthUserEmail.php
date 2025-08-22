<?php

namespace App\Message\Email;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class AuthUserEmail{

    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function registreNewUser(string $userEmail, string $generatedPassword){
        $email = (new Email())
        ->from("no-reply@panoptique.com")
        ->to($userEmail)
        ->subject("Bienvenue sur Panoptique !")
        ->text("Votre compte a été créé. Votre mot de passe est : " . $generatedPassword)
        ->html("
        <p>Bonjour et bienvenue !</p>
        <p>Votre compte a été créé avec succès.</br></p>
        <p>Voici votre mot de passe temporaire : <strong> \"".$generatedPassword."\" </strong></p>
        <p>Nous vous recommandons de vous connecter et de changer ce mot de passe dès que possible.</p>
        <p>Cordialement,</p>
        <p>L'équipe Panoptique</p>"
        );

        $this->mailer->send($email);
    }

    public function resetPassword(string $userEmail, string $generatedPassword){
        $email = (new Email())
        ->from("no-reply@panoptique.com")
        ->to($userEmail)
        ->subject("Votre nouveau mot de passe pour Panoptique !")
        ->text("Voici votre nouveau mot de passe : ".$generatedPassword)
        ->html("
        <p>Bonjour</p>
        <p>Votre mot de passe pour votre compte sur l'application Panoptique a été réinitialisé.</br></p>
        <p>Voici votre nouveau mot de passe temporaire :<strong>>\"".$generatedPassword."\"</strong></p>
        <p>Nous vous recommandons de vous connecter et de changer ce mot de passe dès que possible.</p>
        <p>Cordialement,</p>
        <p>L'équipe Panoptique</p>
        ");

        $this->mailer->send($email);
    }

    
}