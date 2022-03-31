<?php

namespace App\Controller\Admin;

use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_USER') && user.getIsVerified() === true", message: 'Page Introuvable', statusCode:404)]
#[Route('/user/edit')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_edit')]
    public function edit(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (($form['setProfilePicture'])->getData() !== null)
            {
                $extension = $form['setProfilePicture']->getData()->guessExtension();
                if (!$extension || !in_array($extension, ["jpg", "png", "jpeg"])) {
                    throw new UploadException('Seuls les formats jpg, png et jpeg sont acceptés');
                }
                $files = $form['setProfilePicture']->getData();
                if ($user->getProfilePicture())
                {
                    $files->move('../public/uploads/user/', $user->getProfilePicture());
                }
                else
                {
                    $nameUserWithoutSpace = str_replace(" ", "", $user->getFullName());
                    $nameUserLower = strtolower($nameUserWithoutSpace);
                    $setFileName = $nameUserLower.'_USER_'.rand(1, 999).'.'.$extension ;
                    $user->setProfilePicture($setFileName);
                    $files->move('../public/uploads/user/', $user->getProfilePicture());
                }
            }
            $userRepository->add($user);
            $this->addFlash('success', 'Profil modifiée avec succès');
            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/password', name: 'app_user_edit_password')]
    public function editPassword(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, [], ['current_password_is_required'=> true ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($encodedPassword);
            $userRepository->add($user);
            $this->addFlash('success', 'Mot de passe modifié avec succès');
            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
        }
        return $this->render('security/reset_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
