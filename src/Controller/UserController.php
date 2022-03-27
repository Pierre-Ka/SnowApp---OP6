<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/edit', name: 'schtroumpf')]
    public function edit(Request $request, UserRepository $userRepository): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $userRepository->add($user);
            $this->addFlash('success', 'Profil modifiée avec succès');
            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
