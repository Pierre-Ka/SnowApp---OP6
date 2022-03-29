<?php

namespace App\Security;

use App\Entity\User;
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
    private EntityManagerInterface $em;
    private TokenGeneratorInterface $tokenGenerator;

    public function __construct(UrlGeneratorInterface $router, MailerInterface $mailer, EntityManagerInterface $manager, TokenGeneratorInterface $generator)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->em = $manager;
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
        $user->setToken($token);
        $user->setExpiresToken(new \DateTime('+1 week'));
        $this->em->flush();
        $extraParams['token'] = $token;
        $uri = $this->router->generate($routeName, $extraParams, UrlGeneratorInterface::ABSOLUTE_URL);
        return $uri;
    }

    public function verifyUser(Request $request, UserInterface $user): bool
    {
        $now = new \Datetime;
        if ($user->getExpiresToken() < $now) {
            $user->eraseCredentials();
            return false;
        }
        $user->setIsVerified(true);
        $user->eraseCredentials();
        $this->em->flush();
        return true;
    }
    public function verifyUserForResetPassword(Request $request, UserInterface $user): bool
    {
        $now = new \Datetime;
        if ($user->getExpiresToken() < $now) {
            $user->eraseCredentials();
            return false;
        }
        return true;
    }
}
