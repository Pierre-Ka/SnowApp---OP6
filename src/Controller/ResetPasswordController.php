<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ForgetPasswordType;
use App\Security\EmailHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    private EntityManagerInterface $em;
    private EmailHandler $emailHandler;

    public function __construct(EntityManagerInterface $em, EmailHandler $emailHandler)
    {
        $this->em = $em;
        $this->emailHandler = $emailHandler;
    }

    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        if ($this->getUser()) {
            $this->addFlash('error', 'Vous etes déjà connecté');
            return $this->redirectToRoute('app_trick_index');
        }

        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->em->getRepository(User::class)->findOneByEmail($form['email']->getData());
            if (($user->getToken() === null) && ($user->getIsVerified() === true)) {
                $this->emailHandler->sendForgottenPasswordMail($user);
                $this->addFlash('info', 'Un email vous a été envoyé pour pour réinitialiser votre mot de passe');
                return $this->render('security/sending_reset_request.html.twig');
            }
            $this->addFlash('error', 'Un email de notre part vous a déjà été envoyé');
            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks']);
        }
        return $this->render('security/forget_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/check-email', name: 'app_change_password_authenticator')]
    public function checkEmail(Request $request): Response
    {
        $idUser = ((int)$request->query->get('key') / 11324);
        $user = $this->em->getRepository(User::class)->findOneById($idUser);

        if ($this->emailHandler->verifyUserForResetPassword($request, $user)) {
            $token = $request->query->get('token');
            $request->getSession()->set('user', $user);
            $request->getSession()->set('token', $token);
            return $this->redirectToRoute('app_change_password', ['token' => $token]);
        } else {
            $this->addFlash('error', 'Une erreur est survenue, veuillez réessayer');
            return $this->redirectToRoute('app_forgot_password_request');
        }
    }


    #[Route('/reset/{token}', name: 'app_change_password')]
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                          string  $token): Response
    {

        if (!$request->getSession()->get('user')) {
            $this->addFlash('error', 'Une erreur est survenue, veuillez réessayer');
            return $this->redirectToRoute('app_forgot_password_request');
        }
        $userSession = $request->getSession()->get('user');
        $user = $this->em->getRepository(User::class)->findOneById($userSession->getId());

        if ($user->getToken() === $request->getSession()->get('token') && $user->getToken() === $token) {
            $form = $this->createForm(ChangePasswordType::class, []);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $encodedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                );
                $user->setPassword($encodedPassword);
                $this->em->persist($user);
                $this->em->flush();
                $user->eraseCredentials();
                $request->getSession()->clear();
                $this->addFlash('success', 'Votre mot de passe a bien été modifié, veuillez vous connecter.');
                return $this->redirectToRoute('app_login');
            }
            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        $this->addFlash('error', 'Une erreur est survenue, veuillez réessayer');
        return $this->redirectToRoute('app_forgot_password_request');
    }
}
