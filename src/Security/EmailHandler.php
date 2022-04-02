<?php

namespace App\Security;

use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/*
 * Email Generator and Authenticate Handler **
 */
class EmailHandler
{
    private UrlGeneratorInterface $router;
    private MailerInterface $mailer;
    private UserManager $userManager;
    private TokenGeneratorInterface $tokenGenerator;

    public function __construct(UrlGeneratorInterface $router, MailerInterface $mailer, UserManager $userManager, TokenGeneratorInterface $generator)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->userManager = $userManager;
        $this->tokenGenerator = $generator;
    }

    public function sendConfirmationMail(UserInterface $user)
    {
        $link = $this->generateUrlForEmailConfirmation('app_verify_email', $user);
        $email = (new TemplatedEmail())
            ->from('SnowTrick@noreply.com')
            ->to($user->getEmail())
            ->subject('Confirmer votre email')
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'link' => $link,
                'user' => $user,
                    ] );
        $this->mailer->send($email);
    }

    public function sendForgottenPasswordMail(UserInterface $user)
    {
        $link = $this->generateUrlForEmailConfirmation('app_change_password', $user);
        $email = (new TemplatedEmail())
            ->from('SnowTrick@noreply.com')
            ->to($user->getEmail())
            ->subject('Reinitialisation de votre mot de passe')
            ->htmlTemplate('emails/reset_request.html.twig')
            ->context([
                'link' => $link,
                'user' => $user,
            ] );
        $this->mailer->send($email);
    }

    public function generateUrlForEmailConfirmation(string $routeName, UserInterface $user, array $extraParams = []): string
    {
        $token = $this->tokenGenerator->generateToken();
        $this->userManager->defineTokenRelatedProperties($user, $token);
        $extraParams['token'] = $token;
        $uri = $this->router->generate($routeName, $extraParams, UrlGeneratorInterface::ABSOLUTE_URL);
        return $uri;
    }
}
