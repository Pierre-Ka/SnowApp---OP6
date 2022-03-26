<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Security\EmailHandler;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    private EmailHandler $emailHandler;
    private Security $security;

    public function __construct(EmailHandler $emailHandler, Security $security)
    {
        $this->emailHandler = $emailHandler;
        $this->security = $security;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator): Response
    {
        if ($this->getUser()) {
            $this->addFlash('error', 'Vous êtes déjà connecté');
            return $this->redirectToRoute('app_trick_index');
        }
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsVerified(false);
            $em->persist($user);
            $em->flush();
            $this->emailHandler->sendConfirmationMail($user);
            $this->addFlash('info', 'Un email vous a été envoyé pour confimer votre inscription');
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        return $this->render('security/registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email/', name: 'app_verify_email')]
    public function verify(Request $request): Response
    {
        $user = $this->security->getUser();
        if ($user->getIsVerified() === true) {
            $this->addFlash('info', 'Votre adresse email a dejà été vérifiée');
            return $this->redirectToRoute('app_trick_index');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->emailHandler->verifyUser($request, $user))
        {
            $this->addFlash('success', 'Votre adresse email a été correctement vérifiée');
        }
        else
        {
            $this->addFlash('error', 'Votre adresse email n\'a pas été correctement vérifiée');
        }
        return $this->redirectToRoute('app_trick_index');
    }
}

