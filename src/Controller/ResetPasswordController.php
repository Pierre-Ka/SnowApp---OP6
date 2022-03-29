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
            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks']);
        }

        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->em->getRepository(User::class)->findOneByEmail($form['email']->getData());
            if(!$user){
                $this->addFlash('error', 'Aucun compte associé a l\'email entré !');
                return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks']);
            }
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

    #[Route('/reset/{token}', name: 'app_change_password')]
    public function reset(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher,
                          string  $token): Response
    {
        if (!$user) // INUTILE A CAUSE DU PARAM CONVERTER ?
        {
            $this->addFlash('error', 'Une erreur est survenue, veuillez réessayer');
            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks']);
        }
        if ($this->emailHandler->verifyUserForResetPassword($request, $user))
        {
            $form = $this->createForm(ChangePasswordType::class, ['current_password_is_required'=> false ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $encodedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                );
                $user->setPassword($encodedPassword);
                $user->eraseCredentials();
                $this->em->flush();
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

