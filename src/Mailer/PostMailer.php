<?php

namespace App\Mailer;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PostMailer
{
    /** @var MailerInterface */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send()
    {
        $message = (new Email())
            ->from('adresse@dawan.fr')
            ->to('destinataire@example.com')
            ->cc('responsable@example.com')
            ->subject('Une nouvelle publication a été enregistrée.')
            ->html('<p>La version en HTML</p>')
            ->text('alternative textuelle')
        ;
        $this->mailer->send($message);
    }
}